<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use App\Models\LabTestOrder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LabController extends Controller
{
    /**
     * Lab Dashboard: Overview metrics, recent tests, lab distribution.
     */
    public function dashboard()
    {
        $totalLabs = Lab::count();
        $activeLabs = Lab::where('status', 'active')->count();
        $inactiveLabs = $totalLabs - $activeLabs;

        $totalTests = LabTest::count();
        $activeTests = LabTest::where('status', 'active')->count();
        $inactiveTests = $totalTests - $activeTests;

        $totalCategories = LabTestCategory::count();
        $totalOrders = LabTestOrder::count();

        $recentTests = LabTest::with(['lab', 'category'])
            ->latest()
            ->take(10)
            ->get();

        $topLabs = Lab::withCount('tests')
            ->orderBy('tests_count', 'desc')
            ->take(5)
            ->get();

        $categories = LabTestCategory::withCount('tests')
            ->orderBy('tests_count', 'desc')
            ->get();

        return view('admin.lab.dashboard', compact(
            'totalLabs',
            'activeLabs',
            'inactiveLabs',
            'totalTests',
            'activeTests',
            'inactiveTests',
            'totalCategories',
            'totalOrders',
            'recentTests',
            'topLabs',
            'categories'
        ));
    }

    /**
     * Lab Setup: Manage laboratory details.
     */
    public function index(Request $request)
    {
        $query = Lab::withCount(['tests', 'activeTests']);

        // Search by name, city, state, contact
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('state', 'LIKE', "%{$search}%")
                    ->orWhere('contact_number', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $labs = $query->latest()->paginate(12)->withQueryString();

        return view('admin.lab.labs', compact('labs'));
    }

    /**
     * Store a new Laboratory.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'description'    => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:20',
            'status'         => 'required|in:active,inactive',
        ]);

        try {
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_lab_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/labs');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $logoPath = 'uploads/labs/' . $filename;
            }

            Lab::create([
                'name'           => $request->name,
                'logo'           => $logoPath,
                'description'    => $request->description,
                'contact_number' => $request->contact_number,
                'email'          => $request->email,
                'address'        => $request->address,
                'city'           => $request->city,
                'state'          => $request->state,
                'pincode'        => $request->pincode,
                'status'         => $request->status,
            ]);

            return redirect()->back()->with('success', 'Laboratory added successfully!');
        } catch (Exception $e) {
            Log::error('Lab store error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error adding laboratory: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing Laboratory.
     */
    public function update(Request $request, $id)
    {
        $lab = Lab::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'description'    => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:20',
            'status'         => 'required|in:active,inactive',
        ]);

        try {
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($lab->logo && File::exists(public_path($lab->logo))) {
                    File::delete(public_path($lab->logo));
                }

                $file = $request->file('logo');
                $filename = time() . '_lab_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/labs');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $lab->logo = 'uploads/labs/' . $filename;
            }

            $lab->name           = $request->name;
            $lab->description    = $request->description;
            $lab->contact_number = $request->contact_number;
            $lab->email          = $request->email;
            $lab->address        = $request->address;
            $lab->city           = $request->city;
            $lab->state          = $request->state;
            $lab->pincode        = $request->pincode;
            $lab->status         = $request->status;
            $lab->save();

            return redirect()->back()->with('success', 'Laboratory updated successfully!');
        } catch (Exception $e) {
            Log::error('Lab update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating laboratory: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Lab active/inactive status.
     */
    public function toggleStatus($id)
    {
        $lab = Lab::findOrFail($id);
        $lab->status = $lab->status === 'active' ? 'inactive' : 'active';
        $lab->save();

        return redirect()->back()->with('success', "Lab status updated to {$lab->status}!");
    }

    /**
     * Delete a Laboratory.
     */
    public function destroy($id)
    {
        try {
            $lab = Lab::findOrFail($id);

            if ($lab->logo && File::exists(public_path($lab->logo))) {
                File::delete(public_path($lab->logo));
            }

            $lab->delete();

            return redirect()->back()->with('success', 'Laboratory deleted successfully!');
        } catch (Exception $e) {
            Log::error('Lab delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting laboratory: ' . $e->getMessage());
        }
    }

    /**
     * Lab Test Orders (Prepared for future use).
     */
    public function orders(Request $request)
    {
        $query = LabTestOrder::with(['labTest', 'lab', 'user']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'LIKE', "%{$search}%")
                    ->orWhere('patient_name', 'LIKE', "%{$search}%")
                    ->orWhere('patient_phone', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => LabTestOrder::count(),
            'pending'   => LabTestOrder::where('status', 'pending')->count(),
            'completed' => LabTestOrder::where('status', 'completed')->count(),
            'cancelled' => LabTestOrder::where('status', 'cancelled')->count(),
        ];

        return view('admin.lab.orders', compact('orders', 'stats'));
    }
}

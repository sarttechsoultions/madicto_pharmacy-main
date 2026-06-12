<?php

namespace App\Http\Controllers;

use App\Models\CoustmerMedicineModel;
use App\Models\medicineModel;
use App\Models\OrdersModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function dashboard()
    {
        $totalorders = OrdersModel::count();

        $totalcoustmermedicine = CoustmerMedicineModel::count();

        $useractive = User::where('status', 'Active')->count();

        $totalmedicine = medicineModel::count();

        // RECENT ORDERS
        $recentorders = OrdersModel::with([
            'user',
            'items'
        ])
            ->latest()
            ->take(5)
            ->get();

        // LOW STOCK
        $lowstockmedicines = medicineModel::where(
            'quantity',
            '<',
            10
        )->get();

        // TOP SELLING MEDICINES
        $topSellingMedicines = DB::table('order_items')
            ->select(
                'medicine_id',
                'medicine_name',
                DB::raw('SUM(quantity) as total_sales')
            )
            ->groupBy(
                'medicine_id',
                'medicine_name'
            )
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        $maxSales = $topSellingMedicines->max('total_sales');

        foreach ($topSellingMedicines as $medicine) {

            $medicine->percentage =
                $maxSales > 0
                ? ($medicine->total_sales / $maxSales) * 100
                : 0;
        }

        // WEEKLY REVENUE
        $weeklyRevenue = OrdersModel::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as revenue')
        )
            ->whereDate(
                'created_at',
                '>=',
                Carbon::now()->subDays(6)
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // MONTHLY REVENUE
        $monthlyRevenue = OrdersModel::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as revenue')
        )
            ->whereYear(
                'created_at',
                Carbon::now()->year
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // CHART DATA
        $weeklyLabels = $weeklyRevenue->map(function ($item) {

            return Carbon::parse(
                $item->date
            )->format('D');
        });

        $weeklyData = $weeklyRevenue->pluck(
            'revenue'
        );

        $monthlyLabels = $monthlyRevenue->map(function ($item) {

            return Carbon::create()
                ->month($item->month)
                ->format('M');
        });

        $monthlyData = $monthlyRevenue->pluck(
            'revenue'
        );

        // TOTAL REVENUE
        $totalRevenue = OrdersModel::sum(
            'total_amount'
        );

        return view(
            'admin.dashboard',
            compact(
                'totalorders',
                'totalcoustmermedicine',
                'useractive',
                'totalmedicine',
                'recentorders',
                'lowstockmedicines',
                'topSellingMedicines',
                'weeklyLabels',
                'weeklyData',
                'monthlyLabels',
                'monthlyData',
                'totalRevenue'
            )
        );
    }

    public function webIndex()
    {
        $medicines = medicineModel::latest()->take(8)->get();
        return view('web.index', compact('medicines'));
    }
}

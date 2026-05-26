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

        $recentorders = OrdersModel::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $lowstockmedicines = medicineModel::where('quantity', '<', 10)
            ->get();

        // TOP SELLING
        $topSellingMedicines = OrdersModel::select(
            'medicine_id',
            DB::raw('COUNT(*) as total_sales')
        )
            ->with('medicine')
            ->groupBy('medicine_id')
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
        $weeklyRevenue = OrdersModel::join(
            'medicine',
            'orders.medicine_id',
            '=',
            'medicine.id'
        )
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(medicine.price) as revenue')
            )
            ->whereDate(
                'orders.created_at',
                '>=',
                Carbon::now()->subDays(6)
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // MONTHLY REVENUE
        $monthlyRevenue = OrdersModel::join(
            'medicine',
            'orders.medicine_id',
            '=',
            'medicine.id'
        )
            ->select(
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(medicine.price) as revenue')
            )
            ->whereYear(
                'orders.created_at',
                Carbon::now()->year
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // CHART DATA
        $weeklyLabels = $weeklyRevenue->map(function ($item) {
            return Carbon::parse($item->date)->format('D');
        });

        $weeklyData = $weeklyRevenue->pluck('revenue');

        $monthlyLabels = $monthlyRevenue->map(function ($item) {
            return Carbon::create()->month($item->month)->format('M');
        });

        $monthlyData = $monthlyRevenue->pluck('revenue');

        return view('admin.dashboard', compact(
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
            'monthlyData'
        ));
    }
}

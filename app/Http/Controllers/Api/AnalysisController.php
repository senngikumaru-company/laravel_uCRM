<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Services\AnalysisService;
use App\Services\DecileService;
use App\Services\RFMService;


class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $subQuery = Order::betweenDate($request->startDate, $request->endDate);

        // 日別
        if($request->type === 'perDay'){
            list($data, $labels, $totals) = AnalysisService::perDay($subQuery);
        }

        // 月別
        if($request->type === 'perMonth'){
            list($data, $labels, $totals) = AnalysisService::perMonth($subQuery);
        }

        // 年別
        if($request->type === 'perYear'){
            list($data, $labels, $totals) = AnalysisService::perYear($subQuery);
        }

        // decile
        if($request->type === 'decile'){
            list($data, $labels, $totals) = DecileService::decile($subQuery);
        }

        // RFM分析
        if($request->type === 'rfm'){
            list($data, $totals, $eachCount) = RFMService::rfm($subQuery, $request->rfmPrms);

            return response()->json([
                'data' => $data,
                'type' => $request->type,
                'eachCount' => $eachCount,
                'totals' => $totals,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'data' => $data,
            'type' => $request->type,
            'labels' => $labels,
            'totals' => $totals,
        ], Response::HTTP_OK);

    }
}

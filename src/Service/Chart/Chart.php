<?php

namespace App\Service\Chart;

use App\Service\Chart\ChartData;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\VAxis;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ComboChart;

class Chart{

    const ANIMATION_STARTUP = true;
    const ANIMATION_DURATION = 1000;
    const CHART_AREA_HEIGHT = '80%';
    const CHART_AREA_WIDTH = '80%';

    private $chartData;


    public function __construct(ChartData $chartData)
    {
        $this->chartData = $chartData;
    }

    /**
     * Create the Value graph
     *
     * @return ComboChart
     */
    public function getValueGraph($client){
        $datas = $this->chartData->getValueAndQuantityStock($client);
        $datas = array_reverse($datas);
        $datas_convert[] = ['Jour','Valeur(€)'];

        // dump($datas);

        if ($datas == []) {
            $datas_convert[] = [
                date_format(new \Datetime('now'),'d/m/Y'),
                0
            ];
        };

        foreach($datas as $data){
            $datas_convert [] = [
                date_format($data['date'], 'd/m'),
                intval($data['value'])
            ];
            
        };
        // dump($datas_convert);

        $chart = new LineChart();
        $chart->getData()->setArrayToDataTable($datas_convert);
        $chart->getOptions()->setTitle('Valeur des articles en stock par jour');
        $chart->getOptions()->setColors(['#113d3e']);
        $chart->getOptions()->setHeight(500);
        $chart->getOptions()->setWidth(1000);
        $chart->getOptions()->setCurveType('function');
        $chart->getOptions()->setLineWidth(4);
        $chart->getOptions()->setPointsVisible(true);
        $chart->getOptions()->setPointSize(10);
        $chart->getOptions()->getLegend()->setPosition('right');
        // $chart->getOptions()->getAnimation()->setStartup(self::ANIMATION_STARTUP);
        // $chart->getOptions()->getAnimation()->setDuration(self::ANIMATION_DURATION);
        // $chart->getOptions()->getChartArea()->setHeight(self::CHART_AREA_HEIGHT);
        // $chart->getOptions()->getChartArea()->setWidth(self::CHART_AREA_WIDTH);

        $vAxisAmount = new VAxis();
        $vAxisAmount->setTitle('Montant en €');
        $chart->getOptions()->setVAxes([$vAxisAmount]);

        // $seriesAmount = new \CMEN\GoogleChartsBundle\GoogleCharts\Options\ComboChart\Series();
        // $seriesAmount->setType('lines');
        // $seriesAmount->setTargetAxisIndex(0);
        // $chart->getOptions()->setSeries([$seriesAmount]);

        // $chart->getOptions()->getHAxis()->setTitle('Année');
        // $chart->getOptions()->setColors(['#f6dc12',]);

        return $chart;
    }

    /**
     * Create the Quantity graph
     *
     * @return ComboChart
     */
    public function getQuantityGraph($client){
        $datas = $this->chartData->getValueAndQuantityStock($client);
        $datas = array_reverse($datas);
        $datas_convert[] = ['Jour','Quantité'];

        // dump($datas);

        if ($datas == []) {
            $datas_convert[] = [
                date_format(new \Datetime('now'),'d/m/Y'),
                0
            ];
        };

        foreach($datas as $data){
            $datas_convert [] = [
                date_format($data['date'], 'd/m'),
                $data['quantity']
            ];
            
        };
        // dump($datas_convert);

        $chart = new LineChart();
        $chart->getData()->setArrayToDataTable($datas_convert);
        $chart->getOptions()->setTitle('Nombres d\'articles en stock par jour');
        $chart->getOptions()->setColors(['#c3cf01']);
        $chart->getOptions()->setHeight(500);
        $chart->getOptions()->setWidth(1000);
        $chart->getOptions()->setCurveType('function');
        $chart->getOptions()->setLineWidth(4);
        $chart->getOptions()->setPointsVisible(true);
        $chart->getOptions()->setPointSize(10);
        $chart->getOptions()->getLegend()->setPosition('right');
        // $chart->getOptions()->getAnimation()->setStartup(self::ANIMATION_STARTUP);
        // $chart->getOptions()->getAnimation()->setDuration(self::ANIMATION_DURATION);
        // $chart->getOptions()->getChartArea()->setHeight(self::CHART_AREA_HEIGHT);
        // $chart->getOptions()->getChartArea()->setWidth(self::CHART_AREA_WIDTH);
        $vAxisAmount = new VAxis();
        $vAxisAmount->setTitle('Quantité');
        $chart->getOptions()->setVAxes([$vAxisAmount]);

        return $chart;
    }
    
}
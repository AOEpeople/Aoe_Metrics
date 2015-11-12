<?php

interface Aoe_Metrics_Model_CollectorInterface
{

    public function put($metricName, $value, $unit=null, array $dimensions=array());

    public function flush();

}
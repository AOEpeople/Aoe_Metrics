<?php
/**
 * An interface for Metric models to use. An event observer will contain an
 * instance of a collector.
 */
interface Aoe_Metrics_Model_MetricInterface
{
    /**
     * Given an observer, collect some metrics and put them to the collector
     * (Aoe_Metrics_Model_CollectorInterface)
     * @param  Varien_Event_Observer $observer
     * @return self
     */
    public function collect(Varien_Event_Observer $observer);
}
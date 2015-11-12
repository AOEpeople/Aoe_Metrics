<?php

class Aoe_Metrics_Model_Collector_CloudWatch implements Aoe_Metrics_Model_CollectorInterface
{
    protected $namespace;

    protected $data = array();

    protected $defaultDimensions = array();

    protected $awsCliPath;

    protected $awsCliProfile;

    public function __construct()
    {
        $this->namespace = Mage::getStoreConfig('system/aoemetrics/cloudwatch_namespace');
        if (empty($this->namespace)) {
            throw new Exception('Invalid namespace');
        }
        $this->awsCliPath = Mage::getStoreConfig('system/aoemetrics/cloudwatch_awscli_path');
        if (empty($this->awsCliPath) || !is_executable($this->awsCliPath)) {
            throw new Exception('Invalid aws cli path');
        }
        $this->awsCliProfile = Mage::getStoreConfig('system/aoemetrics/cloudwatch_awscli_profile');

        $defaultDimensions = explode(',', Mage::getStoreConfig('system/aoemetrics/cloudwatch_default_dimensions'));
        foreach ($defaultDimensions as $defaultDimension) {
            list($k, $v) = array_map('trim', explode('=', $defaultDimension));
            if ($k && $v) {
                $this->defaultDimensions[] = array('Name' => $k, 'Value' => $v);
            }
        }
    }

    public function put($metricName, $value, $unit=null, array $dimensions=array())
    {
        if (!is_null($unit) && !in_array($unit, explode('|', 'Seconds|Microseconds|Milliseconds|Bytes|Kilobytes|Megabytes|Gigabytes|Terabytes|Bits|Kilobits|Megabits|Gigabits|Terabits|Percent|Count|Bytes/Second|Kilobytes/Second|Megabytes/Second|Gigabytes/Second|Terabytes/Second|Bits/Second|Kilobits/Second|Megabits/Second|Gigabits/Second|Terabits/Second|Count/Second|None'))) {
            throw new InvalidArgumentException('Invalid unit');
        }

        $dim = $this->defaultDimensions;
        foreach ($dimensions as $k => $v) {
            $dim[] = array('Name' => $k, 'Value' => $v);
        }

        $this->data[] = array(
            'MetricName' => $metricName,
            'Value' => floatval($value),
            'Unit' => !is_null($unit) ? $unit : 'None',
            'Dimensions' => $dim
            // 'Timestamp'
            // 'StatisticValues'
        );
    }

    public function flush()
    {
        $tempfile = tempnam(sys_get_temp_dir(), 'aoemetrics_');
        if ($tempfile === false) {
            throw new Exception('Error while writing creating tempfile');
        }
        file_put_contents($tempfile, json_encode($this->data));

        $command = array();
        $command[] = escapeshellcmd($this->awsCliPath);
        if ($this->awsCliProfile) {
            $command[] = '--profile '.escapeshellarg($this->awsCliProfile);
        }
        $command[] = 'cloudwatch';
        $command[] = 'put-metric-data';
        $command[] = '--namespace '.escapeshellarg($this->namespace);
        $command[] = '--metric-data file://'.$tempfile;

        $command = implode(' ', $command);

        $output = array();
        $returnVar = null;

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception('Error whle executing command. Output: ' . implode("\n", $output));
        }

        unlink($tempfile);
        $this->data = array();
    }

}
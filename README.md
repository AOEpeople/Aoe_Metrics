# Aoe_Metrics (for AWS CloudWatch)

Author: [Fabrizio Branca](https://twitter.com/fbrnc)

This is a first prototype. Simplicity was a priority.
This is why this doesn't pull in any other libraries (e.g. https://github.com/beberlei/metrics) 
and relies on the `aws` cli command and its profiles instead of pulling in AWS SDK for PHP.

Also, this currently only supports [AWS CloudWatch](https://aws.amazon.com/cloudwatch/) and no other collector (StatsD, CollectD,...)

New metrics can be added by subscribing to the `aoemetrics_collect` event.

Metrics will be collected in a cron job (which is also called `aoemetrics_collect`).
 
**NOTE:** This cron job does not come with a default schedule. Use Aoe_Scheduler to configure it to be run "always". 

Also, currently there's no interface to select which metrics are being collected and to pass any configuration to the metrics.
You need to handle this inside your metric implementation. 

Look at `Aoe_Metrics_Model_Metric_OrderStateCount` for an example.

### Configuration

See `System > Configuration > System > AOE Metrics`

### Ideas for metrics

* Order count by state (implemented)
* Order count by status (might result in a lot of data)
* EE Indexer Queue sizes - processed, unprocessed (should go into https://github.com/AOEpeople/Aoe_EeIndexerStats)
* Aoe_Queue stats (should go into https://github.com/AOEpeople/Aoe_Queue)
* Aoe_Scheduler stats (failed jobs, delay between scheduled_at and executed_at) (should go into https://github.com/AOEpeople/Aoe_Scheduler)
* Products Updated (number of new and updated products for a given timeframe)
* Sessions
* Customers (logins, registrations)

### Note

You need to keep track of the last execution yourself in the metric implementation if your metric relies on that.
Example: Counting number of updated products or customer signup in a given timeframe. 
Assuming that the last run was exactly 60 seconds ago is probably not very accurate and the metrics might show wrong numbers.

### Contributions

If you have an interesting metric that's general purpose feel free to add it to this module and create a PR. 
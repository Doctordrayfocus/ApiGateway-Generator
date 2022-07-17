<?php

namespace App\Jobs;

class AsyncRequest extends Job
{
    private $requestData;
    protected $currentService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // make request

        try {
            $serviceName = "\App\Services\\" . $this->requestData['service'];

            $this->currentService = new $serviceName(false);

            $this->currentService->{$this->requestData['method']}(isset($this->requestData['param']) ? $this->requestData['param'] : null);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}

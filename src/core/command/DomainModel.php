<?php

namespace FTumiwan\DomainCore\Command;

use Illuminate\Console\Command;
use FTumiwan\DomainCore\Command\Scaffold;

class DomainModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'ftumiwan:domainmodel';

    protected $signature = 'ftumiwan:domainmodel {domainname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold for business domain model';

    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $domainname = $this->argument('domainname');
        $scaff = new Scaffold($domainname);
        $scaff->new();
    }
}
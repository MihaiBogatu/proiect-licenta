<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Hash;
use Symfony\Component\Console\Output; // pentru afisare in consola

class RegAdmin extends Command
{

	public $email = "bogatumihai1@yahoo.com";
	public $parola = "bogatumihai98";
	public $nume = "Bogatu";
	public $rol = "administrator";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RegAdmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$output = new Output\ConsoleOutput();

		$utilizator = User::where('email', $this->email)
						  ->get();
		if(count($utilizator)==0){
			$utilizator = new User;
			$utilizator -> name = $this->nume;
			$utilizator -> email = $this->email;
			$utilizator -> password = Hash::make($this->parola);
			$utilizator -> rol = $this->rol;
			$utilizator->save();
			$output->writeln('Admin inregistrat');

		}else{
			$output->writeln('Admin-ul deja exista');
		}

        return 0;
    }
}

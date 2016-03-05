<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use DB;  //I NEED THIS TO QUERY DATABASE
use Mail; //USE THIS TO SEND EMAILS
use App\Http\Controllers\Schema;



class SalesforceUserController extends Controller
{

    public function index(){
    	$string = 'Call GET success';
    	return $string;
    }

    public function sf(){
    	//SHOW COLUMNS FROM my_table;
    	// $user = DB::table('users')->where('email', $email)->first();
    	$users = DB::table('users')->get();
    	$table = 'users';
    	$columns = Schema::getColumnListing($table);
    	var_dump($columns);
    }

    public function store(Request $request) {
		$name      = $request->input('name');
		$email     = $request->input('email');
		$pass      = $request->input('pass');
		$sf        = $request->input('salesforce_id');

		$validation = $this->validate($request, [
            'name'          => 'required',
            'email'         => 'required|email',
            'pass'          => 'required',
            'salesforce_id' => 'required'
		]);    	

		if($validation == null){
			// make sure authcontroller.php are also receiving this
			//user.php add both columns
			// CHECK IF EMAIL EXIST IN TABLE
			$user = DB::table('users')->where('email', $email)->first();
			if($user != null){
			    return 'Already a portal user';
			}else{
				//USER NOT IN SYSTEM, THEN CREATE
				$array = array(
							    'name'          => $name,
							    'email'         => $email,
							    'password'      => bcrypt($pass),
							    'salesforce_id' => $sf,
							);

				$createUser = User::create($array);
				return 'Success Creation';
			}			
		}else{
			return 'Not pass validation';
		}
	}

	public static function genericAPICalls($headers, $method, $data, $url) {

		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		switch($method){

			case 'GET':
			break;

			case 'POST':
				curl_setopt($handle, CURLOPT_POST, true);
				curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
			break;

			case 'PUT':
				curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
			break;

			case 'DELETE':
				curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
			break;
		}

		$response = curl_exec($handle);
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		$result['response'] = $response;
		$result['code'] = $code;

		return $result;		
	}

	public static function sendEmail(){
		/*
		$password = 'abc123';
		$user = User::findOrFail(1);
		Mail::send('emails.welcome', ['user' => $user], function ($m) use ($user) {
            $m->from('postmaster@fabianmanzano.info', 'Your Application');

            $m->to('manzanofab@hotmail.com')->subject('Your Reminder!');
        });
        */
        $data = array();
        Mail::send('home', $data, function($message) {
		    $message->to('manzanofab@hotmail.com','Codelution Staff')->subject('Welcome');
		});
        return 'hello baby';
        // return view('emails.welcome');
	}
	
	public function sendToMinITFake(){


		$headers = array(
			'Accept: application/json',
			'Content-Type: application/x-www-form-urlencoded',
			);

		$data = 'username=demo&access=site_dBhBhgCw&transcode=9001&ClientNumber=000123&ClientStatus=Pending&DateOfBirth=01-02-2016&FirstName=John&LastName=Mary&Gender=M&MaritalStatus=Single&NumberOfChildren=0&IncomeFrequency=Fortnight&EnableEmail=N&EnableFax=N&EnableSMS=N&GrossIncomeAmount=0&HomeOwnership=No&Email=man@man.com&MobilePhone=123&StreetNumberName=12%20strete&StreetState=QLD&StreetSuburb=Gold%20coast&StreetPostcode=4217&AddressSince=2006/Feb';

		$data = 'username=demo&access=site_dBhBhgCw&transcode=5003&ClientNumber=132465&LastName=man';

		$url = "https://api.min-it.net/http/data/";
		$method = 'POST';
		
		$result = SalesforceUserController::genericAPICalls($headers, $method, $data, $url);

		$response = $result['response'];
		$code = $result['code'];

		var_dump($code);
		
		$responseArray = explode('|', $response);

		list($ClientNumber, $ClientStatus, $ClientTitle, $FirstName, $MiddleName, $LastName, $DateOfBirth, $IdNumber, $VedaNumbers, $HomePhone, $WorkPhone, $MobilePhone, $Fax, $Email, $Gender, $MaritalStatus, $NumberOfChildren, $SpecialNote, $HomeOwnership, $HomeLoanProvider, $RealEstateAgentName, $RealEstateAgentPhone, $Occupation, $GrossIncomeAmount, $IncomeFrequency, $EmploymentStatus, $EmployerName, $EmployerAddress, $EmployerContactName, $EmployerPhone, $Defaults) = explode("~", $responseArray[1]);

		var_dump($responseArray);
	}
}
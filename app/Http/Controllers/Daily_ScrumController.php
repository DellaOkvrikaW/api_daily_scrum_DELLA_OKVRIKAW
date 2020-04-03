<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Daily_Scrum; 
use DB;
use Illuminate\Support\Facades\Validator;

class Daily_ScrumController extends Controller
{
  
    public function index($id)
    {
    	try{
        $dataUser = User::where('id', $id)->first();
        if($dataUser != NULL){
	        $data["count"] = Daily_Scrum::count();
          $Daily_Scrum = array();
          $dataDailyScrum = DB::table('daily_scrum')->join('user','user.id','=','daily_scrum.id_user')
                                                    ->select ('daily_scrum.id', 'user.firstname', 'user.lastname', 'daily_scrum.team', 'daily_scrum.activity_yesterday', 'daily_scrum.activity_today', 'daily_scrum.problem_yesterday', 'daily_scrum.solution')
                                                    ->where('daily_scrum.id_user','=', $id)
                                                    ->get();

	        foreach ($dataDailyScrum as $p) {
	            $item = [
                  "id"          		    => $id,   
                  "firstname"           => $p->firstname,
                  "lastname"            => $p->lastname,                
	                "team"                => $p->team,
	                "activity_yesterday"  => $p->activity_yesterday,
	                "activity_today"    	=> $p->activity_today,
                  "problem_yesterday"   => $p->problem_yesterday,
                  "solution"    	      => $p->solution,


                ];

	            array_push($Daily_Scrum, $item);
	        }
	        $data["Daily_Scrum"] = $Daily_Scrum;
	        $data["status"] = 1;
	        return response($data);

        } else {
          return response([
            'status' => 0,
            'message' => 'Data User tidak ditemukan'
          ]);
        }
	    } catch(\Exception $e){
			return response()->json([
			  'status' => '0',
			  'message' => $e->getMessage()
			]);
      	}
    }

    public function getAll($limit = 10, $offset = 0, $id_user)
    {
    	try{
	        $data["count"] = Daily_Scrum::count();
	        $daily_scrum = array();
	        $dataDaily = DB::table('daily_scrum')->join('user','user.id','=','daily_scrum.id_user')
                                               ->select('daily_scrum.id', 'daily_scrum.id_user','user.firstname','user.lastname','user.email',
                                               'daily_scrum.team','daily_scrum.activity_yesterday','daily_scrum.activity_today',
                                               'daily_scrum.problem_yesterday','daily_scrum.solution')
                                               ->skip($offset)
                                               ->take($limit)
	                                             ->get();
	        foreach ($dataDaily as $p) {
	            $item = [
                  "id"          		    => $p->id,
                  "id_user"       	    => $p->id_user, 
                  "firstname"           => $p->firstname,
                  "lastname"            => $p->lastname,                   
	                "team"                => $p->team,
	                "activity_yesterday"  => $p->activity_yesterday,
	                "activity_today"    	=> $p->activity_today,
                  "problem_yesterday"   => $p->problem_yesterday,
                  "solution"    	      => $p->solution,
	            ];

	            array_push($daily_scrum, $item);
	        }
	        $data["daily_scrum"] = $daily_scrum;
	        $data["status"] = 1;
	        return response($data);

	    } catch(\Exception $e){
			return response()->json([
			  'status' => '0',
			  'message' => $e->getMessage()
			]);
      	}
    }
    public function store(Request $request)
    {
      try{
    		$validator = Validator::make($request->all(), [
          'id_user'               => 'required|numeric',
          'team'			  	        => 'required|string|max:255',
          'activity_yesterday'	  => 'required|string|max:255',
          'activity_today'	      => 'required|string|max:255',
          'problem_yesterday'		  => 'required|string|max:255',
          'solution'			        => 'required|string|max:255',
    		]);

    		if($validator->fails()){
    			return response()->json([
    				'status'	=> 0,
    				'message'	=> $validator->errors()
    			]);
    		}

    		//cek apakah ada id user tersebut
    		if(User::where('id', $request->input('id_user'))->count() > 0){
            $data = new Daily_Scrum();
            $data->id_user              = $request->input('id_user');
            $data->team                 = $request->input('team');
            $data->activity_yesterday   = $request->input('activity_yesterday');
            $data->activity_today       = $request->input('activity_today');
            $data->problem_yesterday    = $request->input('problem_yesterday');
            $data->solution             = $request->input('solution');
            $data->save();

		    		return response()->json([
		    			'status'	=> '1',
		    			'message'	=> 'Data berhasil ditambahkan!'
		    		], 201);
    		} else {
    			return response()->json([
	                'status' => '0',
	                'message' => 'Data tidak ditemukan.'
	            ]);
    		}

      } catch(\Exception $e){
            return response()->json([
                'status' => '0',
                'message' => $e->getMessage()
            ]);
        }
  	}

    public function delete($id)
    {
        try{

            $delete = Daily_Scrum::where("id", $id)->delete();

            if($delete){
              return response([
                "status"  => 1,
                  "message"   => "Data Daily Scrum berhasil dihapus."
              ]);
            } else {
              return response([
                "status"  => 0,
                  "message"   => "Data Daily Scrum gagal dihapus."
              ]);
            }
            
        } catch(\Exception $e){
            return response([
            	"status"	=> 0,
                "message"   => $e->getMessage()
            ]);
        }
    }
}

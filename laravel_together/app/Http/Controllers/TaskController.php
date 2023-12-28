<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\BaseData;

class TaskController extends Controller
{
    // 대시보드 show
    public function showdashboard()
    {

        // --- 유저 정보
        $user = Auth::user();
        // --- 현재 날짜 출력
        $now = Carbon::now();
        $formatDate1 = $now->format('Y년 n월 j일');
        // $formatDate2 = $now->format('G시 i분');

        // --- 현재 요일 출력
        $koreanDayOfWeek = $now->isoFormat('dddd');

        // --- 대시보드 공지 출력
        $dashboardNotice = DB::table('tasks as t')
        ->join('projects as p','p.id','=','t.project_id')
        ->join('project_users as pu','pu.project_id','=','p.id')
        ->join('basedata as b','p.color_code_pk','=','b.data_content_code')
        ->select ('t.title', 't.content', 'p.color_code_pk', 'p.project_title', 'b.data_content_name')
        ->where('b.data_title_code', '=', 3)
        ->where('pu.member_id', '=', $user->id)
        ->where('t.category_id','=', 1)
        ->get();

        // --- 사이드바 출력
        $userId = Auth::id();

        $project0title = DB::table('projects as p')
        ->join('project_users as pu', 'p.id','=','pu.project_id')
        ->join('basedata as b', 'b.data_content_code', '=', 'p.color_code_pk')
        ->select('p.project_title', 'b.data_content_name', 'p.id')
        ->where('pu.member_id', '=', $userId)
        ->where('p.flg','=', 0)
        ->where('b.data_title_code', '=', 3)
        ->orderBy('p.created_at', 'asc')
        ->get();

        $project1title = DB::table('projects as p')
        ->join('project_users as pu', 'p.id','=','pu.project_id')
        ->join('basedata as b', 'b.data_content_code', '=', 'p.color_code_pk')
        ->select('p.project_title', 'b.data_content_name', 'p.id')
        ->where('pu.member_id', '=', $userId)
        ->where('p.flg','=', 1)
        ->where('b.data_title_code', '=', 3)
        ->orderBy('p.created_at', 'asc')
        ->get();

       // --- 프로젝트 진척도 출력
    $projectIdData = DB::table('project_users')
    ->select('project_id')
    ->where('member_id', '=', $userId)
    ->get();

    $projectIds = $projectIdData->pluck('project_id')->toArray(); // 프로젝트 아이디 배열로 변환
    $completionPercentages = [];

    foreach ($projectIds as $projectId) {
    $completionPercentage = DB::table('tasks as t')
        ->join('projects as p', 't.project_id', '=', 'p.id')
        ->join('basedata as b', 'p.color_code_pk', '=', 'b.data_content_code')
        ->selectRaw('ROUND((SUM(CASE WHEN t.task_status_id = 3 THEN 1 ELSE 0 END) / COUNT(t.project_id)) * 100) AS completion_percentage, b.data_content_name')
        ->where('t.project_id', '=', $projectId)
        ->where('b.data_title_code', '=', 3)
        ->groupBy('b.data_content_name')
        ->get();

    $completionPercentages[$projectId] = $completionPercentage;
    }

        // dd($completionPercentages);

    // 상태현황 개수 출력
    $before =DB::table('tasks')
                ->join('project_users', function($join){
                    $join->on('project_users.project_id','=','tasks.project_id');
                })
                ->selectRaw('count(tasks.task_status_id) as cnt')
                ->where('project_users.member_id',$user->id)
                ->where('tasks.category_id',0)
                ->where('tasks.task_status_id',0)
                ->groupBy('tasks.task_status_id')
                ->get();
    // dump($before);

    $ing =DB::table('tasks')
            ->join('project_users', function($join){
                $join->on('project_users.project_id','=','tasks.project_id');
            })
            ->selectRaw('count(tasks.task_status_id) as cnt')
            ->where('project_users.member_id',$user->id)
            ->where('tasks.category_id',0)
            ->where('tasks.task_status_id',1)
            ->groupBy('tasks.task_status_id')
            ->get();
    // dump($ing);

    $feedback =DB::table('tasks')
                ->join('project_users', function($join){
                    $join->on('project_users.project_id','=','tasks.project_id');
                })
                ->selectRaw('count(tasks.task_status_id) as cnt')
                ->where('project_users.member_id',$user->id)
                ->where('tasks.category_id',0)
                ->where('tasks.task_status_id',2)
                ->groupBy('tasks.task_status_id')
                ->get();
    // dump($feedback);

    $complete =DB::table('tasks')
                ->join('project_users', function($join){
                    $join->on('project_users.project_id','=','tasks.project_id');
                })
                ->selectRaw('count(tasks.task_status_id) as cnt')
                ->where('project_users.member_id',$user->id)
                ->where('tasks.category_id',0)
                ->where('tasks.task_status_id',3)
                ->groupBy('tasks.task_status_id')
                ->get();
    // dd($complete);


    //데이터 담을 빈 객체 생성
    $baseObj = new \stdClass();
    $baseObj->cnt = 0;
    $statuslist = [
    'before'=> count($before) === 0 ? collect([$baseObj]) : $before,
    'ing'=> count($ing) === 0 ? collect([$baseObj]) : $ing,
    'feedback'=> count($feedback) === 0 ? collect([$baseObj]) : $feedback,
    'complete'=> count($complete) === 0 ? collect([$baseObj]) : $complete
    ];
    // dd($statuslist);

    // d-day 데이터 출력
    $dday_data = DB::table('tasks as tk')
                    ->join('project_users as pu', function($project_users){
                        $project_users->on('pu.project_id','=','tk.project_id');
                    })
                    ->join('projects as pj', function($projects){
                        $projects->on('pj.id','=','tk.project_id');
                    })
                    ->join('basedata as bd', function($basedata){
                        $basedata->on('bd.data_content_code','=','pj.color_code_pk');
                    })
                    ->select('tk.title', 'tk.end_date','pj.color_code_pk','bd.data_content_name',DB::raw('tk.end_date - date(NOW()) as dday'))
                    ->where('pu.member_id',$user->id)
                    ->where('tk.task_depth', '0') //상위업무만 출력
                    ->where('bd.data_title_code','3')
                    ->where('tk.category_id','0')
                    ->orderBy('dday','desc')
                    ->get();
    // dd($dday_data);
    $group_dday = $dday_data->groupBy(function($item){
        return $item->dday;
    });

    // dd($group_dday);


        if (Auth::check()) {
            return view('dashboard', [
                'user' => $user,
                'formatDate1' => $formatDate1,
                // 'formatDate2' => $formatDate2,
                'koreanDayOfWeek' => $koreanDayOfWeek,
                'dashboardNotice' => $dashboardNotice,
                'project0title' => $project0title,
                'project1title' => $project1title,
                'completionPercentages' => $completionPercentages,
                'statuslist' => $statuslist,
                'dday_data' => $dday_data,
                'group_dday' =>$group_dday
            ]);
        } else {
            return redirect('/user/login');
        }
    }

    // 대시보드 그래픽 데이터
    public function board_graph_data(Request $request) {

        $user = Auth::user();

        $before =DB::table('tasks')
        ->join('project_users', function($join){
            $join->on('project_users.project_id','=','tasks.project_id');
        })
        ->selectRaw('count(tasks.task_status_id) as cnt')
        ->where('project_users.member_id',$user->id)
        ->where('tasks.category_id',0)
        ->where('tasks.task_status_id',0)
        ->groupBy('tasks.task_status_id')
        ->get();
        // dump($before);

        $ing =DB::table('tasks')
            ->join('project_users', function($join){
                $join->on('project_users.project_id','=','tasks.project_id');
            })
            ->selectRaw('count(tasks.task_status_id) as cnt')
            ->where('project_users.member_id',$user->id)
            ->where('tasks.category_id',0)
            ->where('tasks.task_status_id',1)
            ->groupBy('tasks.task_status_id')
            ->get();
        // dump($ing);

        $feedback =DB::table('tasks')
                ->join('project_users', function($join){
                    $join->on('project_users.project_id','=','tasks.project_id');
                })
                ->selectRaw('count(tasks.task_status_id) as cnt')
                ->where('project_users.member_id',$user->id)
                ->where('tasks.category_id',0)
                ->where('tasks.task_status_id',2)
                ->groupBy('tasks.task_status_id')
                ->get();
        // dump($feedback);

        $complete =DB::table('tasks')
                ->join('project_users', function($join){
                    $join->on('project_users.project_id','=','tasks.project_id');
                })
                ->selectRaw('count(tasks.task_status_id) as cnt')
                ->where('project_users.member_id',$user->id)
                ->where('tasks.category_id',0)
                ->where('tasks.task_status_id',3)
                ->groupBy('tasks.task_status_id')
                ->get();
        // dd($complete);

  
        //데이터 담을 빈 객체 생성
        $baseObj = new \stdClass();
        $baseObj->cnt = 0;
        $statuslist = [
          'before'=> count($before) === 0 ? collect([$baseObj]) : $before,
          'ing'=> count($ing) === 0 ? collect([$baseObj]) : $ing,
          'feedback'=> count($feedback) === 0 ? collect([$baseObj]) : $feedback,
          'complete'=> count($complete) === 0 ? collect([$baseObj]) : $complete
        ];
        // dd($statuslist);
  
        // Log::debug("Response : ", $statuslist);
        // Log::debug("***** project_graph_data End *****");
        return response()->json($statuslist);
        // return '반환 테스트';
      }

    // 태스크 전체 조회 (수정이전)
    public function index()
    {
        $data = [];
        // 프로젝트와 업무들을 모두 호출 (나중에 조건 추가가능, 허나 정렬은 여기서 못함, TODO: project_id와 task_parent의 관계성 정해야 함)
        $project = Project::project_depth();
        foreach ($project as $key => $value) {            
            $value->depth_0 = Task::depth_pj(0,$value->id); // 모델에서 만들어 놓은 쿼리로 하위 업무 각자 가져옴
            foreach ($value->depth_0 as $key2 => $value2) {            
                $value2->depth_1 = Task::depth_tsk(1,$value2->id);
                $data[] = $project[$key];
            }
        }
        // // $data = $depth_0;
        // $data['task'] = $depth_0;
        // dd($data);

        // --- 유저 정보
        $user = Auth::user();

        $user_data = project::where('user_pk',$user->id)
        ->select('id'
                ,'user_pk'
                ,'color_code_pk'
                ,'project_title'
                ,'project_content'
                ,'start_date'
                ,'end_date'
                ,'created_at'
                ,'flg'
                )
        ->get();

        // --- 대시보드 공지 출력
        $dashboardNotice = DB::table('tasks as t')
        ->join('projects as p','p.id','=','t.project_id')
        ->join('project_users as pu','pu.project_id','=','p.id')
        ->join('basedata as b','p.color_code_pk','=','b.data_content_code')
        ->select ('t.title', 't.content', 'p.color_code_pk', 'p.project_title', 'b.data_content_name')
        ->where('b.data_title_code', '=', 3)
        ->where('pu.member_id', '=', $user->id)
        ->get();

        // --- 사이드바 출력
        $userId = Auth::id();

        $project0title = DB::table('projects as p')
        ->join('project_users as pu', 'p.id','=','pu.project_id')
        ->join('basedata as b', 'b.data_content_code', '=', 'p.color_code_pk')
        ->select('p.project_title', 'b.data_content_name', 'p.id')
        ->where('pu.member_id', '=', $userId)
        ->where('p.flg','=', 0)
        ->where('b.data_title_code', '=', 3)
        ->orderBy('p.created_at', 'asc')
        ->get();

        $project1title = DB::table('projects as p')
        ->join('project_users as pu', 'p.id','=','pu.project_id')
        ->join('basedata as b', 'b.data_content_code', '=', 'p.color_code_pk')
        ->select('p.project_title', 'b.data_content_name', 'p.id')
        ->where('pu.member_id', '=', $userId)
        ->where('p.flg','=', 1)
        ->where('b.data_title_code', '=', 3)
        ->orderBy('p.created_at', 'asc')
        ->get();

        return view('modal.modalgantt')
        ->with('data', $data)
        ->with('project0title', $project0title)
        ->with('project1title', $project1title)
        ->with('user', Session::get('user'));
    }

    // 프로젝트별 간트차트
    public function index_one($id)
    {
        $data = [];
        // 프로젝트와 업무들을 모두 호출 (나중에 조건 추가가능, 허나 정렬은 여기서 못함, TODO: project_id와 task_parent의 관계성 정해야 함)
        $data['project'] = Project::find($id);
        $depth_0 = Task::depth_pj(0,$id); // 모델에서 만들어 놓은 쿼리로 하위 업무 각자 가져옴
        // $data = $depth_0;
        foreach ($depth_0 as $key => $value) {            
            $value->depth_1 = Task::where('task_depth',1)->where('task_parent',$value->id)->get()->toArray();
        }
        $data['task'] = $depth_0;
        dd($data);
        return view('modal.modalgantt')->with('data', $data)->with('user', Session::get('user'));
    }

    // 상세 업무/공지 조회
    public function view($id)
    {
        $result['task'] = Task::task_detail($id);
        // Log::debug($result);
        $result['children'] = Task::task_detail_children($id);
        $result['comment'] = Task::task_detail_comment($id);

        // task->depth 값을 보고 부모를 데려올지 결정
        return $result;
        if ($result['task'][0]->task_depth !== '0') {
            $result['parents'] = Task::task_detail_parents($result['task'][0]->task_depth, $id);
        }

        return $result;
    }

    // 업무 작성
    public function store(Request $request)
    {
        // 반환 틀
        $responseData = [
            "code" => "0",
            "msg" => "",
            "data" => []
        ];
        // Log::debug('request: ' , $request->all());

        // 입력받은 데이터로 pk(id) 추출
        // Log::debug('cookie: '.$request->cookie('user'));
        // Log::debug('Auth: '. Auth::id());
        if($request['task_status_id']){            
            $sta = DB::table('basedata as status')
            ->where('data_title_code', 0)
            ->where('data_content_name', $request['task_status_id'])
            ->select('data_content_code','data_content_name')
            ->get();
        } else if($request->task_status_id){            
            $sta = DB::table('basedata as status')
            ->where('data_title_code', 0)
            ->where('data_content_name', $request->task_status_id)
            ->select('data_content_code','data_content_name')
            ->get();
        } 
        // Log::debug('상태: ' , $sta->toArray());
        if($request['priority_id']){   
            $pri = DB::table('basedata')
                ->where('data_title_code', 1)
                ->where('data_content_name', $request['priority_id'])
                ->select('data_content_code','data_content_name')
                ->get();
        } else if($request->priority_id){ 
            $pri = DB::table('basedata')
            ->where('data_title_code', 1)
            ->where('data_content_name', $request->priority_id)
            ->select('data_content_code','data_content_name')
            ->get();
        }
        // Log::debug('순위: ' , $pri->toArray());
        if($request['task_responsible_id']){   
            $res = DB::table('users')
                ->where('name', $request['task_responsible_id'])
                ->select('id','name')
                ->get();
        } else if($request->task_responsible_id){ 
                $res = DB::table('users')
                    ->where('name', $request->task_responsible_id)
                    ->select('id','name')
                    ->get();
        }
        // Log::debug('user_id: ' , $res->toArray());
        if($request['project_id']){   
            $tsk_num = DB::table('tasks')
                ->where('project_id', $request['project_id'])
                ->where('category_id', $request['category_id'])
                ->orderBy('task_number', 'desc')
                ->first();
        } else if($request->project_id){ 
            $tsk_num = DB::table('tasks')
            ->where('project_id', $request->project_id)
            ->where('category_id', $request->category_id)
            ->orderBy('task_number', 'desc')
            ->first();
        }
        if($request['start_date']){   
            $res = DB::table('tasks')
                ->where('start_date', $request['start_date'])
                ->get();
        } else if($request->start_date){ 
                $res = DB::table('tasks')
                    ->where('start_date', $request->start_date)
                    ->get();
        }
        if($request['end_date']){   
            $res = DB::table('tasks')
                ->where('end_date', $request['end_date'])
                ->get();
        } else if($request->task_responsible_id){ 
                $res = DB::table('tasks')
                    ->where('end_date', $request->end_date)
                    ->get();
        }
        Log::debug([$tsk_num]);
        // Log::debug($tsk_num['task_number']);

        // 이메일 추가 시 대비
        // $eml = DB::table('users')->where('email', $request['email'])->first();
        
        // 입력 컨텐츠 유효성 검사
        // $tit = $request['title']; // TODO: 유효성 처리 추가
        // $con = $request['content']; // TODO: 유효성 처리 추가
        // $request['title'] = $tit;
        // $request['content'] = $con;

        // nullable
        if(!empty($sta[0])){
            $request['task_status_id'] = $sta[0]->data_content_code;
            $responseData['names']['task_status_name'] = $sta[0]->data_content_name;
        } else {
            $request['task_status_name'] = null;
        }
        if(!empty($res[0])){
            $request['task_responsible_id'] = $res[0]->id;
            if(isset($responseData['names'])){
                $responseData['names']['task_responsible_name'] = $res[0]->name;
            }
        } else {
            $request['task_responsible_name'] = null;
        }
        if(!empty($pri[0])){
            $request['priority_id'] = $pri[0]->data_content_code;
            if(isset($responseData['names'])){
                $responseData['names']['priority_name'] = $pri[0]->data_content_name;
            }
        } else {
            $request['priority_name'] = null;
        }

        // not null
        $nowUser = Auth::id();
        $request['task_writer_id'] = $nowUser;
        // $request['category_id'] = ;
        if(isset($tsk_num)){        
            $request['task_number'] = $tsk_num->task_number + 1;
        } else {
            $request['task_number'] = 1;
        }
        // if(isset($request['task_parent'])){
        //     $result['task_parent'] = $request['task_parent'];
        // }
        
        // $request['start_date'] = $start;
        // $request['end_date'] = $end;
        Log::debug($request);
        
        // 업무 생성 및 반환 분기
        $result = Task::create($request->toArray());
        // Log::debug($result);
        if (!$result) {
            $responseData['msg'] = 'task not created.';
            $responseData['data'] = $result;
        } else {
            $responseData['msg'] = 'task created.';
            $responseData['data'] = $result;
        }

        return $responseData;
    }

    // 업무 수정
    public function update(Request $request, $id)
    {
        $responseData = [
            "code" => "0",
            "msg" => "",
            "data" => []
        ];

        $result = Task::find($id);
        Log::debug('$request :' . $request);
        // Log::debug($result->data);

        if (!$result) {
            $responseData["code"] = "E01";
            $responseData["msg"] = "No Data.";
        } else {
            $res = User::where('name', $request['task_responsible_id'])->first();
            $sta = DB::table('basedata')->where('data_title_code',0)->where('data_content_name', $request['task_status_id'])->first();
            $pri = DB::table('basedata')->where('data_title_code',1)->where('data_content_name', $request['priority_id'])->first();
            Log::debug('$request :' . $request);
            Log::debug('$res :' . $res->data_content_code);
            Log::debug('$sta :' . $sta->data_content_code);
            $result->task_responsible_id = $res->data_content_code;
            $result->task_status_id = $sta->data_content_code;
            $result->priority_id = $pri->id;
            Log::debug('$request->title :' . $request->title);
            $result->title = $request->title;
            Log::debug('$request->content :' . $request->content);
            $result->content = $request->content;
            Log::debug('$request->start_date :' . $request->start_date);
            if ($request->start_date !== '시작일') {
                $result->start_date = $request->start_date;
                Log::debug('$result->start_date :' . $result->start_date);
            }
            Log::debug($request->end_date);
            if ($request->end_date !== '마감일') {
                $result->end_date = $request->end_date;
                Log::debug('$result->end_date :' . $result->end_date);
            }
            $result->save();

            $responseData["code"] = "U01";
            $responseData["msg"] = $id." updated";
            $responseData['data'] = $result;
        }

        return $responseData;
    }
    // 업무 수정 간트버전
    public function ganttUpdate(Request $request, $id)
    {
        $responseData = [
            "code" => "E01",
            "msg" => "no data"
        ];
        
        Log::debug('---------------------------------------'.$id);
        $result = Task::find($id);
        Log::debug('$request :' . $request);
        Log::debug('$result :' . $result);
        foreach ($request->value as $key => $updatedData) {
            if($updatedData != ''){
                Log::debug($updatedData.": 들어온 ".$key."값입니다");
                Log::debug($result->$key.'안에 값이 있습니다');
                $result->$key = $updatedData;
                $result->save();

                $responseData["code"] = "U01";
                $responseData["msg"] = $id." updated";
                $responseData['data'] = $result;
            }
        }


        return $responseData;
    }

    // 업무 삭제
    public function delete(Request $request, $id)
    {
        $responseData = [
            "code" => "0",
            "msg" => ""
        ];
        
        $result = Task::where('id', $id)->delete();

        if (!$result) {
            $responseData['code'] = 'E01';
            $responseData['msg'] = $id . ' is no where';
        } else {
            $responseData['code'] = 'D01';
            $responseData['msg'] = 'task : ' . $id . '->deleted.';
            $responseData['data'] = $id;
        }

        return $responseData;
        // return [$request, $id];
    }
}


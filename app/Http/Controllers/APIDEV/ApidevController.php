<?php
   
namespace App\Http\Controllers\APIDEV;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Job;
use Validator;
use App\Http\Resources\Job as JobResource;
   
class ApidevController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = Job::all();
    
        return $this->sendResponse(JobResource::collection($jobs), 'Jobs retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'months' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $job = Job::create($input);
   
        return $this->sendResponse(new JobResource($job), 'Job created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $job = Job::find($id);
  
        if (is_null($job)) {
            return $this->sendError('Job not found.');
        }
   
        return $this->sendResponse(new JobResource($job), 'Job retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Job $job)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'months' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $job->name = $input['name'];
        $job->detail = $input['months'];
        $job->save();
   
        return $this->sendResponse(new JobResource($job), 'Job updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        $job->delete();
   
        return $this->sendResponse([], 'Job deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Api\Policy;

use App\Http\Controllers\Api\BaseController;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\CompanyPolicy;
use App\Models\Acknowledge;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Validator;

class CompanyPolicyController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/policy/company-policy",
     *     tags={"Company Policy"},
     *     summary="Get all company policies",
     *     description="Returns a list of all company policies based on the authenticated user's permissions.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="New Policy Title"),
     *                     @OA\Property(property="description", type="string", example="Policy description here."),
     *                     @OA\Property(property="attachment", type="string", example="attachment.pdf"),
     *                     @OA\Property(property="created_by", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function index()
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Company Policy') || \Auth::user()->type == 'employee') {
                $companyPolicy = CompanyPolicy::where('created_by', '=', \Auth::user()->creatorId())->get();
                return $this->successResponse(['company_policy'=>$companyPolicy]);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/policy/company-policy/create",
     *     tags={"Company Policy"},
     *     summary="Create company policy",
     *     description="Returns a list of branches for creating a new company policy.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Branch Name")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property *             = "string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function create()
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Company Policy')) {
                $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                return $this->successResponse(['branches'=>$branches]);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/policy/company-policy",
     *     tags={"Company Policy"},
     *     summary="Store a new company policy",
     *     description="Creates a new company policy with the provided details.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id", "title", "description"},
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="New Policy Title"),
     *             @OA\Property(property="description", type="string", example="Policy description here."),
     *             @OA\Property(property="attachment", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Company policy created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="branch", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Policy Title"),
     *                 @OA\Property(property="description", type="string", example="Policy description here."),
     *                 @OA\Property(property="attachment", type="string", example="attachment.pdf"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request)
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Company Policy')) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'branch_id' => 'required|exists:branches,id',
                        'title' => 'required',
                        'description' => 'required',
                        'attachment' => 'mimes:jpeg,png,jpg,gif,pdf,doc,zip|max:20480',
                    ]
                );

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $fileNameToStore = '';
                if (!empty($request->attachment)) {
                    $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('attachment')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $dir = storage_path('uploads/companyPolicy/');

                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $request->file('attachment')->storeAs('uploads/companyPolicy/', $fileNameToStore);
                }

                $policy = new CompanyPolicy();
                $policy->branch = $request->branch_id;
                $policy->title = $request->title;
                $policy->description = $request->description;
                $policy->attachment = $fileNameToStore;
                $policy->created_by = \Auth::user()->creatorId();
                $policy->save();

               // slack 
                $setting = Utility::settings(\Auth::user()->creatorId());
                $branch = Branch::find($request->branch_id);
                if (isset($setting['company_policy_notification']) && $setting['company_policy_notification'] == 1) {
                    $msg = $request->title . ' ' . __("for") . ' ' . $branch->name . ' ' . __("created") . '.';
                    Utility::send_slack_msg($msg);
                }

                // telegram 
                $setting = Utility::settings(\Auth::user()->creatorId());
                $branch = Branch::find($request->branch_id);
                if (isset($setting['telegram_company_policy_notification']) && $setting['telegram_company_policy_notification'] == 1) {
                    $msg = $request->title . ' ' . __("for") . ' ' . $branch->name . ' ' . __("created") . '.';
                    Utility::send_telegram_msg($msg);
                }

                return $this->successResponse(['company_policy' => $policy],__('Company policy successfully created.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
     /**
     * @OA\Get(
     *     path="/api/policy/company-policy/{id}",
     *     tags={"Company Policy"},
     *     summary="Get a specific company policy",
     *     description="Returns details of a specific company policy.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="branch", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Policy Title"),
     *                 @OA\Property(property="description", type="string", example=" Policy description here."),
     *                 @OA\Property(property="attachment", type="string", example="attachment.pdf"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company Policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company Policy not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function show(CompanyPolicy $companyPolicy)
    {
        try {
            $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $authEmpId = \Auth::user()->employee->id;
            $isAcknowledged = Acknowledge::where('emp_id', $authEmpId)
                ->where('company_policy_id', $companyPolicy->id)
                ->exists();
            return $this->successResponse(compact('branches', 'companyPolicy', 'isAcknowledged'));
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/policy/company-policy/{id}/edit",
     *     tags={"Company Policy"},
     *     summary="Edit a specific company policy",
     *     description="Returns details of a specific company policy for editing.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="branch", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Policy Title"),
     *                 @OA\Property(property="description", type="string", example="Policy description here."),
     *                 @OA\Property(property="attachment", type="string", example="attachment.pdf"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company Policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company Policy not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function edit($id)
    {
        try {   
            $company_policy = CompanyPolicy::find($id);
            if (is_null($company_policy)) {
                return $this->errorResponse(__('Company Policy not found.'), 404);
            }
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Company Policy')) {
                $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                return $this->successResponse(compact('branches', 'company_policy'));
            } else {
                return $ $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/policy/company-policy/{id}/update",
     *     tags={"Company Policy"},
     *     summary="Update a specific company policy",
     *     description="Updates the details of a specific company policy.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id", "title", *             "description"},
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Updated Policy Title"),
     *             @OA\Property(property="description", type="string", example="Updated policy description here."),
     *             @OA\Property(property="attachment", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company policy updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="branch", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Policy Title"),
     *                 @OA\Property(property="description", type="string", example="Updated policy description here."),
     *                 @OA\Property(property="attachment", type="string", example="updated_attachment.pdf"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company Policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company Policy not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $companyPolicy = CompanyPolicy::find($id);
            if (is_null($companyPolicy)) {
                return $this->errorResponse(__('Company Policy not found.'), 404);
            }
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Company Policy')) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'branch_id' => 'required|exists:branches,id',
                        'title' => 'required',
                        'description' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $fileNameToStore = $companyPolicy->attachment; // Keep existing attachment if not updated
                if ($request->hasFile('attachment')) {
                    $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('attachment')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $dir = storage_path('uploads/companyPolicy/');

                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $request->file('attachment')->storeAs('uploads/companyPolicy/', $fileNameToStore);
                }

                $companyPolicy->branch = $request->branch_id;
                $companyPolicy->title = $request->title;
                $companyPolicy->description = $request->description;
                $companyPolicy->attachment = $fileNameToStore;
                $companyPolicy->created_by = \Auth::user()->creatorId();
                $companyPolicy->save();

                return $this->successResponse(['company_policy' =>$companyPolicy], __('Company policy successfully updated.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/policy/company-policy/{id}",
     *     tags={"Company Policy"},
     *     summary="Delete a specific company policy",
     *     description="Deletes a specific company policy.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Company policy deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company Policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company Policy not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message.")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function destroy($id)
    {
        try {
            $companyPolicy = CompanyPolicy::find($id);
            if (is_null($companyPolicy)) {
                return $this->errorResponse(__('Company Policy not found.'), 404);
            }
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Delete Document')) {
                if ($companyPolicy->created_by == \Auth::user()->creatorId()) {
                    $companyPolicy->delete();

                    $dir = storage_path('uploads/companyPolicy/');
                    if (!empty($companyPolicy->attachment)) {
                        unlink($dir . $companyPolicy->attachment);
                    }

                    return $this->successResponse(null, __('Company policy successfully deleted.'));
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/policy/company-policy/acknowledge",
     *     tags={"Company Policy"},
     *     summary="Acknowledge a company policy",
     *     description="Allows an employee to acknowledge a company policy.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"acknowledge_check", "emp_id", "company_policy_id"},
     *             @OA\Property(property="acknowledge_check", type="boolean", example=true),
     *             @OA\Property(property="emp_id", type="integer", example=101),
     *             @OA\Property(property="company_policy_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Policy acknowledged successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Policy acknowledged successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function acknowledge(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'acknowledge_check' => 'required|accepted',
                'emp_id' => 'required|exists:employees,id',
                'company_policy_id' => 'required|exists:company_policies,id',
            ]);
            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
            }

            Acknowledge::create([
                'emp_id' => $request->emp_id,
                'company_policy_id' => $request->company_policy_id,
            ]);

            return $this->successResponse(null,'Policy acknowledged successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/policy/company-policy/acknowledge/{id}",
     *     tags={"Company Policy"},
     *     summary="Show acknowledgments for a company policy",
     *     description="Returns a list of employees who acknowledged a specific company policy.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="emp_id", type="integer", example=101),
     *                     @OA\Property(property="acknowledged_at", type="string", format="date-time", example="2023-10-01T12:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company Policy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company Policy not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function showAcknowledge($id)
    {
        try {
            $employees = Employee::where('is_active', 1)->get();
            $acknowledges = Acknowledge::where('company_policy_id', $id)->pluck('emp_id')->toArray();

            return $this->successResponse(compact('employees', 'acknowledges', 'id'));
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
}

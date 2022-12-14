<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index($company_id, $service_id)
    {
        $temp = Company::find($company_id);
        if(is_null($temp)){
            return response()->json([
                "message" => "Data not found (company)"
            ],404);
        }else{
            $services = DB::table('services')->where('company_id',"=",$company_id )->get();
            if(count($services)==0){
                return response()->json([
                    "message" => "Data not found"
                ],404);
            }else{
                $customers = DB::table('customers')->where('service_id',"=",$service_id)->get();
                if(count($customers)==0){
                    return response()->json([
                        "message" => "Customer not found"
                    ], 404);
                }else{
                    return response()->json([
                        "data" => $customers
                    ]);
                }
                
            }
        }
    }

    public function store($company, $service, Request $request)
    {
        $temp = Company::find($company);
        if(is_null($temp)){
            return response()->json([
                "message" => "Data not found (company)"
            ],404);
        }else{
            $temp_service = DB::table('services')->where('company_id',"=",$company)->where('id',"=",$service)->get();
            
            if(count($temp_service)==0){
                return response()->json([
                    "message" => "Service with the given ID don't exist"
                ],404);
            }else{
                $request->validate([
                    'name' => ['required','string'],
                    'surname' => ['required','string'],
                    'email' => ['required','string'],
                    'personal_code' => ['required'],
                    'city' => ['required','string'],
                    'street' => ['required','string'],
                    'house_number' => ['required','string'],
                ]);
                $temp = $new_customer = Customer::create(array_merge($request->all(), ['service_id'=> $service]));
                
                return response()->json([
                    "data" => array_merge($request->all(), ['id'=> $temp['id']])
                ],201);
            }
        }
    }

    public function show($company, $service,Customer $customer)
    {
        $temp = Company::find($company);
        if(is_null($temp)){
            return response()->json([
                "message" => "Data not found (company)"
            ],404);
        }else{
            $temp_service = DB::table('services')->where('company_id',"=",$company)->where('id',"=",$service)->get();
            
            if(count($temp_service)==0){
                return response()->json([
                    "message" => "Service with the given ID don't exist"
                ],404);
            }else{
                $temp_customers = DB::table('customers')->where('service_id',"=",$service)->get();
                if(count($temp_customers)==0){
                    return response()->json([
                        "message" => "no customers",
                        "serviceID" => $service
                    ],404);
                }else{
                    return response()->json([
                        "data" => $temp_customers
                    ]);
                }
            }
            
        }
    }

    public function update($company, $service, $customer, Request $request)
    {
        $temp = Company::find($company);
        json_decode($request->getContent());

        if (json_last_error() != JSON_ERROR_NONE) {
            // There was an error
            return response()->json([
                "message" => "Bad json"
            ],400);
        }

        if(is_null($temp)){
            return response()->json([
                "message" => "Data not found (company)"
            ],404);
        }else{
            $temp_service = DB::table('services')->where('company_id',"=",$company)->where('id',"=",$service)->get();
            // $temp_service = Service::find($service);
            if(count($temp_service)==0){
                return response()->json([
                    "message" => "Company don't have service with particular service ID"
                ],404);
            }else{
                $temp_customers = DB::table('customers')->where('service_id',"=",$service)->get();
                if(count($temp_customers)==0){
                    return response()->json([
                        "message" => "no customers",
                        "serviceID" => $service
                    ],404);
                }else{
                    $request->validate([
                        'name' => ['sometimes','required','string'],
                        'surname' => ['sometimes','required','string'],
                        'email' => ['sometimes','required','string'],
                        'personal_code' => ['sometimes','required'],
                        'city' => ['sometimes','required','string'],
                        'street' => ['sometimes','required','string'],
                        'house_number' => ['sometimes','required','string'],
                    ]);
                    $customer_update = Customer::find($customer);
                    $customer_update->update($request->all());

                    return response()->json([
                        "data" => $customer_update
                    ]);
                }
            }
        }
    }

    public function destroy($company, $service, $customer, Request $request)
    {
        $temp = Company::find($company);
        
        if(is_null($temp)){
            return response()->json([
                "message" => "Data not found (company)"
            ],404);
        }else{
            $temp_service = DB::table('services')->where('company_id',"=",$company)->where('id',"=",$service)->get();
            // $temp_service = Service::find($service);
            if(count($temp_service)==0){
                return response()->json([
                    "message" => "Company don't have service with particular service ID"
                ],404);
            }else{
                $temp_customers = DB::table('customers')->where('service_id',"=",$service)->get();
                if(count($temp_customers)==0){
                    return response()->json([
                        "message" => "no customers",
                        "serviceID" => $service
                    ],404);
                }else{
                    
                    $customer_update = Customer::find($customer);
                    $customer_update->forceDelete();

                    return response()->json([
                        "data" => $customer_update
                    ]);
                }
            }
        }
    }
}

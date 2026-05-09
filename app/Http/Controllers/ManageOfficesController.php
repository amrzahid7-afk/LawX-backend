<!-- <?php

// namespace App\Http\Controllers;

// use App\Http\Requests\AddOfficeRequest;
// use Illuminate\Http\Request;
// use App\Services\ManageOfficesService;

// class ManageOfficesController extends Controller
// {
//     public ManageOfficesService $manage_offices_service;

//     public function __construct($manage_offices_service)
//     {
//         $this->manage_offices_service = $manage_offices_service;
//     }
//     public function createOffice(AddOfficeRequest $request)
//     {
//         try {
//             $validated_data = $request->validated();
//             $data = $this->manage_offices_service->createOffice($request);
//             return response()->json([
//                 'status' => $data['status'],
//                 'data' => $data['data']
//             ], $data['code']);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'error' => $e->getMessage(),
//                 'Line' => $e->getLine()
//             ], 500);
//         }
//     }
//     public function getOffices()
//     {
//         try {
//             $data = $this->manage_offices_service->getOffices();
//             return response()->json([
//                 'status' => $data['status'],
//                 'data' => $data['data']
//             ], $data['code']);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'error' => $e->getMessage(),
//                 'Line' => $e->getLine()
//             ], 500);
//         }
//     }
//     public function getOffice($id)
//     {
//         try {
//             $data = $this->manage_offices_service->getOffice($id);
//             return response()->json([
//                 'status' => $data['status'],
//                 'data' => $data['data']
//             ], $data['code']);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'error' => $e->getMessage(),
//                 'Line' => $e->getLine()
//             ], 500);
//         }
//     }
// } -->

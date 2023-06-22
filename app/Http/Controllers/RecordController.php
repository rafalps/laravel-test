<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller {
    /**
     * @OA\Get(
     *    path="/records",
     *    operationId="index",
     *    tags={"Records"},
     *    summary="Get list of records",
     *    description="Get list of records",
     *    @OA\Parameter(name="limit", in="query", description="limit", required=false,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(name="page", in="query", description="the page number", required=false,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function index(Request $request) {
        try {
            $limit = $request->limit ?: 15;
            $records = Record::select('id', 'title')
                ->paginate($limit);

            return response()->json(['status' => 200, 'data' => $records]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *      path="/records",
     *      operationId="store",
     *      tags={"Records"},
     *      summary="Store record in DB",
     *      description="Store record in DB",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"title"},
     *            @OA\Property(property="title", type="string", format="string", example="Test Record Title"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function store(Request $request) {
        try {
            DB::beginTransaction();

            $record = Record::create($request->only('title'));
            DB::commit();

            return response()->json(['status' => 201, 'data' => $record]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *    path="/records/{id}",
     *    operationId="show",
     *    tags={"Records"},
     *    summary="Get Record Detail",
     *    description="Get Record Detail",
     *    @OA\Parameter(name="id", in="path", description="Id of Record", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */
    public function show(Record $record) {
        try {
            return response()->json(['status' => 200, 'data' => $record]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     path="/records/{id}",
     *     operationId="update",
     *     tags={"Records"},
     *     summary="Update record in DB",
     *     description="Update record in DB",
     *     @OA\Parameter(name="id", in="path", description="Id of Record", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"title"},
     *           @OA\Property(property="title", type="string", format="string", example="Test Record Title"),
     *        ),
     *     ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function update(Request $request, $id) {
        try {
            DB::beginTransaction();

            $record = Record::updateOrCreate(['id' => $id], $request->only('title'));
            DB::commit();
            return response()->json(['status' => 200, 'data' => $record]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *    path="/records/{id}",
     *    operationId="destroy",
     *    tags={"Records"},
     *    summary="Delete Record",
     *    description="Delete Record",
     *    @OA\Parameter(name="id", in="path", description="Id of Record", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *         @OA\Property(property="status_code", type="integer", example="200"),
     *         @OA\Property(property="data",type="object")
     *          ),
     *       )
     *      )
     *  )
     */
    public function destroy($id) {
        try {
            Record::find($id)->delete();

            return response()->json(['status' => 200, 'data' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }
}
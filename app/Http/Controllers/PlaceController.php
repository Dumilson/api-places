<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdatePlaceRequest;
use App\Models\Place;
use App\Services\PlaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Place Management API",
 *      description="API para gerenciar lugares",
 *      @OA\Contact(
 *          email="domingosbreganha9@gmail.com"
 *      ),
 * )
 */

class PlaceController extends Controller
{
    protected $placeService;

    public function __construct(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/places",
     *     summary="Lista todos os lugares",
     *     tags={"Places"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filtra os lugares pelo nome",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example=""
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de lugares",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Lista de lugares"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Place Name"
     *                     ),
     *                     @OA\Property(
     *                         property="slug",
     *                         type="string",
     *                         example="place-name"
     *                     ),
     *                     @OA\Property(
     *                         property="city",
     *                         type="string",
     *                         example="City Name"
     *                     ),
     *                     @OA\Property(
     *                         property="state",
     *                         type="string",
     *                         example="State Name"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2024-08-08T15:32:33Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2024-08-08T22:38:54Z"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sem lugares disponíveis",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Sem lugares disponíveis"
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        $places = $this->placeService->getAllPlaces($request);
        if ($places) {
            return Helper::success("Lista de lugares", 200, ["data" => $places]);
        }
        return Helper::error("Sem lugares disponíveis", 404);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/places",
     *     summary="Cria um novo lugar",
     *     tags={"Places"},
     *     security={{ "sanctum":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug", "city", "state"},
     *             @OA\Property(property="name", type="string", example="Place Name"),
     *             @OA\Property(property="city", type="string", example="City Name"),
     *             @OA\Property(property="state", type="string", example="State Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lugar criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lugar criado com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Place Name"),
     *                 @OA\Property(property="slug", type="string", example="place-name"),
     *                 @OA\Property(property="city", type="string", example="City Name"),
     *                 @OA\Property(property="state", type="string", example="State Name"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-08T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-08T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao criar lugar",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao criar lugar")
     *         )
     *     )
     * )
     */

    public function store(StoreUpdatePlaceRequest $request)
    {
        try {
            DB::beginTransaction();
            $place = $this->placeService->save($request->all());
            DB::commit();
            return Helper::success("Lugar criado com sucesso", 201, ["data" => $place]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::error("Erro ao criar lugar", 500, $e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/places/{place}",
     *     summary="Atualiza um lugar existente",
     *     tags={"Places"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *         name="place",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *         description="ID do lugar a ser atualizado"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug", "city", "state"},
     *             @OA\Property(property="name", type="string", example="Updated Place Name"),
     *             @OA\Property(property="city", type="string", example="Updated City Name"),
     *             @OA\Property(property="state", type="string", example="Updated State Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lugar atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lugar atualizado com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Place Name"),
     *                 @OA\Property(property="slug", type="string", example="updated-place-name"),
     *                 @OA\Property(property="city", type="string", example="Updated City Name"),
     *                 @OA\Property(property="state", type="string", example="Updated State Name"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-08T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-08T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao atualizar lugar",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao atualizar lugar")
     *         )
     *     )
     * )
     */
    public function update(StoreUpdatePlaceRequest $request, Place $place)
    {
        try {
            DB::beginTransaction();
            $place = $this->placeService->update($place, $request->all());
            DB::commit();
            return Helper::success("Lugar atualizado com sucesso", 201, ["data" => $place]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::error("Erro ao atualizar lugar", 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/places/{place}",
     *     summary="Deleta um lugar",
     *     tags={"Places"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *         name="place",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *         description="ID do lugar a ser deletado"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lugar deletado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lugar deletado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao deletar lugar",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao deletar lugar")
     *         )
     *     )
     * )
     */
    public function destroy(Place $place)
    {
        try {
            DB::beginTransaction();
            $this->placeService->delete($place);
            DB::commit();
            return Helper::success("Lugar deletado com sucesso", 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::error("Erro ao deletar lugar", 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/places/{place}",
     *     summary="Exibe um lugar específico",
     *     tags={"Places"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *         name="place",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *         description="ID do lugar a ser exibido"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do lugar exibidos com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Detalhes"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Place Name"),
     *                 @OA\Property(property="slug", type="string", example="place-name"),
     *                 @OA\Property(property="city", type="string", example="City Name"),
     *                 @OA\Property(property="state", type="string", example="State Name"),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-08-08T15:32:33Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-08-08T22:38:54Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lugar não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lugar não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao exibir lugar",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao exibir lugar")
     *         )
     *     )
     * )
     */
    public function show(Place $place)
    {
        $data = $this->placeService->show($place);
        return Helper::success("Detalhes", 200, $data);
    }
}

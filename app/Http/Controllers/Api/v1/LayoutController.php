<?php
namespace App\Http\Controllers\Api\v1;

use Auth;
use Config;
use Illuminate\Http\Request;
use App\Models\Definitions\Layout as Definition;
use App\Http\Transformers\Api\v1\Definitions\LayoutTransformer;

class LayoutController extends ApiController
{

	/**
	 * GET /api/v1/layout/definitions
	 *
	 * @param  Request    $request
	 * @param  Definition $definition
	 * @return Response
	 */
	public function definitions(Request $request){
		$this->authorize('index', Definition::class);

		$path = sprintf('%s/%s/', Config::get('app.definitions_path'), Definition::$defDir);
		$layouts = glob($path . '*', GLOB_ONLYDIR);

		foreach($layouts as &$layout){
			$layout = str_replace($path, '', $layout);
		}

		return response()->json([ 'data' => $layouts ]);
	}

	/**
	 * GET /api/v1/layout/{layout_definition}/definition
	 * This endpoint supports 'include'.
	 *
	 * @param  Request    $request
	 * @param  Definition $definition
	 * @return Response
	 */
	public function definition(Request $request, Definition $definition){
		$this->authorize('read', $definition);
		return fractal($definition, new LayoutTransformer)->parseIncludes($request->get('include'))->respond();
	}

}

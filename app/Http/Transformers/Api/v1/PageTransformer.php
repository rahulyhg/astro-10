<?php
namespace App\Http\Transformers\Api\v1;

use App\Models\Page;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\TransformerAbstract as FractalTransformer;
use App\Http\Transformers\Api\v1\Definitions\LayoutTransformer as LayoutDefinitionTransformer;

/**
 * Transforms Page from the database to the correct format for the API to output.
 * @package App\Http\Transformers\Api\v1
 */
class PageTransformer extends FractalTransformer
{

    protected $availableIncludes = [ 'parent', 'revision', 'history', 'site' ];

	public function transform(Page $page)
	{
		$data = [
		    'id' => $page->id,
            'path' => $page->path,
            'slug' => $page->slug,
            'version' => $page->version,
            'depth' => $page->depth,
            'parent_id' => $page->parent_id
        ];
        return $data;
	}

    /**
     * Include associated Parent
     * @param Page $page The Page whose parent to transform.
     * @return FractalItem
     */
    public function includeParent(Page $page)
    {
        if($page->parent) {
            return new FractalItem($page->parent, new PageTransformer, false);
        }
    }

    /**
     * Include associated Revision
     * @param Page $page The Page to transform.
     * @return FractalItem
     */
    public function includeRevision(Page $page, ParamBag $params = null)
    {
        if($page->revision) {
            return new FractalItem($page->revision, new RevisionTransformer( $params->get('full') ), false);
        }
    }

    /**
     * Include associated Site
     * @return FractalItem
     */
    public function includeSite(Page $page)
    {
        if($page->site){
            return new FractalItem($page->site, new SiteTransformer, false);
        }
    }

    /**
     * Include associated Blocks
     *
     * It was decided that API clients would rather consume ordered blocks sorted
     * into regions, rather than duplicating ordering and grouping logic in every client.
     *
     * Some nastiness resides here in order to achieve this, commented below...
     *
     */
    public function includeBlocks(Page $page)
    {
        if(!$page->blocks->isEmpty()){
            // Using sortBy instead of orderBy as the collection might have been eager-loaded
            // Use of groupBy results in a Collection with nested Collections, keyed by 'region_name'.
            $blocksByRegion = $page->blocks->sortBy('order')->groupBy('region_name');

            // Unfortunately Fractal cannot serialize a Collection with nested Collections. We use an
            // inline Transformer to create a FractalItem, serializing nested FractalCollections as we go.
            // We use the current scope to access the manager and also pass it to createData to ensure
            // includes continue to function.
            $scope = $this->getCurrentScope();

            return new FractalItem($blocksByRegion, function(Collection $blocksByRegion) use ($scope){
                foreach($blocksByRegion as $region => $blocks){
                    $collection = new FractalCollection($blocks, new BlockTransformer, false);
                    $blocksByRegion[$region] = $scope->getManager()->createData($collection, 'blocks', $scope)->toArray();
                }

                return $blocksByRegion->toArray();
            }, false);
        }
    }

    /**
     * Include associated Layout/Region definitions
     * @param Page $pagecontent
     * @return FractalItem
     */
    public function includeLayoutDefinition(Page $page)
    {
        $layoutDefinition = $page->getLayoutDefinition();
        return new FractalItem($layoutDefinition, new LayoutDefinitionTransformer, false);
    }

    /**
     * Include History (all Revisions up to and including the current one)
     * @param Page $page The page.
     * @return FractalCollection
     */
    public function includeHistory(Page $page)
    {
        if(!$page->history->isEmpty()){
            return new FractalCollection($page->revision->history, new RevisionTransformer, false);
        }
    }


}

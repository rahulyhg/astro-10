<?php
namespace Tests\Unit\Http\Requests\Api\v1\Page;

use Faker;
use Mockery;
use Validator;
use Carbon\Carbon;
use App\Models\Page;
use App\Models\Block;
use App\Models\Route;
use Tests\Unit\Http\Requests\RequestTestCase;
use App\Http\Requests\Api\v1\Page\PersistRequest;

class PersistRequestTest extends RequestTestCase
{

    protected static $modelClass = Page::class;
    protected static $requestClass = PersistRequest::class;

    protected function getAttrs(Page $page = null, Route $route = null, Block $block = null)
    {
        $page = $page ?: factory(Page::class)->make();
        $route = $route ?: factory(Route::class)->states('withParent')->make([ 'page_id' => $page->getKey() ]);

        $block = $block ?: factory(Block::class)->make();

        return attrs_for($page) + [
            'route' => attrs_for($route),

            'regions' => [
                'test-region' => [
                    0 => attrs_for($block),
                ],
            ],
        ];
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WithValidAttributes_IsValid(){
        $request = $this->mockRequest('POST', $this->getAttrs());
        $validator = $request->getValidatorInstance();

        $this->assertTrue($validator->passes());
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WithoutTitle_IsInvalid(){
        $attrs = $this->getAttrs();
        unset($attrs['title']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('title'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WithEmptyTitle_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['title'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('title'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenTitleIs255Chars_IsValid(){
        $attrs = $this->getAttrs();
        $attrs['title'] = str_repeat('a', 255);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('title'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenTitleIs256Chars_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['title'] = str_repeat('a', 256);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('title'));
    }




    /**
     * @test
     * @group validation
     */
    public function validation_WithoutLayoutName_IsInvalid(){
        $attrs = $this->getAttrs();
        unset($attrs['layout_name']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('layout_name'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WithEmptyLayoutName_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['layout_name'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('layout_name'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenLayoutNameDoesNotExist_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['layout_name'] = 'foobar';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('layout_name'));
    }



    /**
     * @test
     * @group validation
     */
    public function validation_WithoutLayoutVersion_IsInvalid(){
        $attrs = $this->getAttrs();
        unset($attrs['layout_version']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('layout_version'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WithEmptyLayoutVersion_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['layout_version'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('layout_version'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenLayoutVersionDoesNotExist_LayoutNameIsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['layout_version'] = 456;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('layout_name'));
    }


    /**
     * @test
     * @group validation
     */
    public function validation_WithoutIsSite_IsValid(){
        $attrs = $this->getAttrs();
        unset($attrs['is_site']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('is_site'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WithEmptyIsSite_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['is_site'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('is_site'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_IsSiteMustBeBoolean(){
        // Valid as true
        $attrs = $this->getAttrs();
        $attrs['is_site'] = true;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();
        $validator->passes();
        $this->assertEmpty($validator->errors()->get('is_site'));

        // Valid as falsey
        $attrs['is_site'] = 0;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('is_site'));

        // Invalid (not boolean)
        $attrs['is_site'] = 'foobar';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('is_site'));
    }


    /**
     * @test
     * @group validation
     */
    public function validation_WithoutIsPublished_IsValid(){
        $attrs = $this->getAttrs();
        unset($attrs['is_published']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('is_published'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WithEmptyIsPublished_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['is_published'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('is_published'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_IsPublishedMustBeBoolean(){
        // Valid as true
        $attrs = $this->getAttrs();
        $attrs['is_published'] = true;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();
        $validator->passes();
        $this->assertEmpty($validator->errors()->get('is_published'));

        // Valid as falsey
        $attrs['is_published'] = 0;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('is_published'));

        // Invalid (not boolean)
        $attrs['is_published'] = 'foobar';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('is_published'));
    }



    /**
     * @test
     * @group validation
     */
    public function validation_OptionsMustBeArrayWhenPresent(){
        // Valid when array
        $attrs = $this->getAttrs();
        $attrs['options'] = [
            'foo' => 'bar'
        ];

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('options'));

        // Not valid when not array
        $attrs['options'] = 'foobar';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('options'));
    }



    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteParentIdIsPresentAndRouteSlugIsAbsent_IsInvalid(){
        $attrs = $this->getAttrs();
        unset($attrs['route']['slug']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validationWhenRouteParentIdIsPresentAndRouteSlugIsEmpty_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['route']['slug'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteParentIdIsAbsentAndRouteSlugIsAbsent_IsValid(){
        $attrs = $this->getAttrs();
        unset($attrs['route']['slug']);
        unset($attrs['route']['parent_id']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteParentIdIsEmptyAndRouteSlugIsEmpty_IsValid(){
        $attrs = $this->getAttrs();
        $attrs['route']['slug'] = '';
        $attrs['route']['parent_id'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteSlugIs255Chars_IsValid(){
        $attrs = $this->getAttrs();
        $attrs['route']['slug'] = str_repeat('a', 255);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteSlugIs256Chars_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['route']['slug'] = str_repeat('a', 256);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteSlugExistsElsewhereInTheTree_IsValid(){
        $existing = factory(Route::class)->states('withPage', 'withParent')->create();

        $attrs = $this->getAttrs();
        $attrs['route']['slug'] = $existing->slug;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.slug'));

    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteSlugExistsAtSameLevelInTreeAndIsCanonical_IsInvalid(){
        $existing = factory(Route::class)->states('withPage', 'withParent')->create();
        $existing->makeCanonical();

        $attrs = $this->getAttrs();
        $attrs['route']['slug'] = $existing->slug;
        $attrs['route']['parent_id'] = $existing->parent_id;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteSlugExistsAtSameLevelInTreeAndIsNotCanonical_IsValid(){
        $existing = factory(Route::class)->states('withPage', 'withParent')->create();
        $existing->makeCanonical();

        $alternative = factory(Route::class)->create([ 'parent_id' => $existing->parent_id, 'page_id' => $existing->page_id, 'is_canonical' => false ]);

        $attrs = $this->getAttrs();
        $attrs['route']['slug'] = $alternative->slug;
        $attrs['route']['parent_id'] = $alternative->parent_id;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.slug'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenUpdatingAndRouteSlugDoesNotChange_IsValid(){
        $page = factory(Page::class)->create();
        $route = factory(Route::class)->states('withParent')->create([ 'page_id' => $page->getKey() ]);

        $attrs = $this->getAttrs($page, $route);         // Ensure that our attrs match the created page/route

        $request = $this->mockRequest('PUT', $attrs);    // Mock an update request
        $this->mockRoute($request, [ 'page' => $page ]); // Mock the route-model binding

        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.slug'));
    }



    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteSlugIsPresentAndParentIdIsAbsent_IsInvalid(){
        $attrs = $this->getAttrs();
        unset($attrs['route']['parent_id']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('route.parent_id'));
    }

    /**
     * @test
     * @group validation
     */
    public function validationWhenRouteSlugIsPresentAndParentIdIsEmpty_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['route']['parent_id'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('route.parent_id'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteIdIsAbsentAndRouteSlugIsAbsent_IsValid(){
        $attrs = $this->getAttrs();
        unset($attrs['route']['parent_id']);
        unset($attrs['route']['slug']);

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.parent_id'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteSlugIsEmptyAndParentIdIsEmpty_IsValid(){
        $attrs = $this->getAttrs();
        $attrs['route']['parent_id'] = '';
        $attrs['route']['slug'] = '';

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertEmpty($validator->errors()->get('route.parent_id'));
    }

    /**
     * @test
     * @group validation
     */
    public function validation_WhenRouteParentIdDoesNotExist_IsInvalid(){
        $attrs = $this->getAttrs();
        $attrs['route']['parent_id'] = 456;

        $request = $this->mockRequest('POST', $attrs);
        $validator = $request->getValidatorInstance();

        $validator->passes();
        $this->assertCount(1, $validator->errors()->get('route.parent_id'));
    }
}

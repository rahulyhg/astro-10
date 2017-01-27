<?php
namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Page;
use App\Models\Block;
use Illuminate\Http\Request;

class SiteController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// get all the sites
		$pages = Page::sites()->get();

		// load the view
		return view('sites.index')
			->with('pages', $pages);
	}

	public function show(Page $site)
	{
		// Get site details
		$route = $site->route;
		$pagesInSite = $route->descendants()->get();

		$site->path = $route->path;

		return view('sites.show')->with(['route'=>$route, 'site'=> $site, 'pages'=> $pagesInSite]);
	}

	public function edit(Page $site)
	{
		if(!$site->key_page) die("no access");

		// Get site details
		$route = $site->route;
		$pagesInSite = $route->descendants()->get();

		if(!isset($page)){
			$page = $site;
		}

		return view('sites.edit')
			->with(['route'=>$route, 'site'=> $site, 'page'=> $page, 'pages'=> $pagesInSite, 'blocks'=>null]);
	}
}

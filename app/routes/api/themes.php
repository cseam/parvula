<?php

namespace Parvula;

use Exception;
use Parvula\Exceptions\IOException;

/**
 * @api {get} /themes List of themes
 * @apiDescription Get the list of installed themes
 * @apiName Index Themes
 * @apiGroup Theme
 *
 * @apiSuccess (200) {array} Array of Theme
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "mytheme": {...},
 *       "othertheme": {...}
 *     }
 */
$this->get('', function ($req, $res) {
	return $this->api->json($res, app('themes')->index());
})->setName('themes.index');

/**
 * @api {get} /themes/:name Theme information
 * @apiDescription Get a specific theme information
 * @apiName Get Theme
 * @apiGroup Theme
 *
 * @apiParam {String} name Theme unique name
 *
 * @apiSuccess (200) {Object} Theme object
 * @apiError (404) ThemeNotFound
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "layouts": {..},
 *       "name": "My theme",
 *       "infos": {
 *         "description": "...",
 *         "filesType": "php"
 *       }
 *     }
 */
$this->get('/{name}', function ($req, $res, $args) {
	if (false === $result = app('themes')->get($args['name'])) {
		return $this->api->json($res, [
			'error' => 'ThemeNotFound'
		], 404);
	}

	return $this->api->json($res, $result);
})->setName('themes.show');

 /**
  * @api {get} /themes/:name/:field/:subfield Specific Theme field/subfield
  * @apiName Get a Theme field
  * @apiGroup Theme
  *
  * @apiParam {String} name Theme unique name
  * @apiParam {String} field Theme field
  * @apiParam {String} [subfield] Optional Theme subfield
  *
  * @apiSuccess (200) {Object} Theme propriety
  * @apiError (404) FieldDoesNotExists
  *
  * @apiSuccessExample {json} Success-Response:
  *     HTTP/1.1 200 OK
  *     {
  *       "layouts": {"index": "index.html"}
  *     }
  */
$this->get('/{name}/{field}[/{subfield}]', function ($req, $res, $args) {
	$themes = app('themes');
	$theme = $themes->get($args['name']);

	$field = $args['field'];

	if (!isset($theme->{$field})) {
		return $this->api->json($res, [
			'error' => 'FieldDoesNotExists',
			'message' => 'The field `' . $field . '` does not exists'
		], 404); // TODO bad args
	}

	if (!isset($args['subfield'])) {
		return $this->api->json($res, $themes->get($args['name'])->{$field});
	}

	$subfield = $args['subfield'];

	if (!isset($theme->{$field}->{$subfield})) {
		return $this->api->json($res, [
			'error' => 'FieldDoesNotExists',
			'message' => 'The sub field `' . $subfield . '` does not exists'
		], 404); // TODO bad args
	}

	return $this->api->json($res, $themes->get($args['name'])->{$field}->{$subfield});
})->setName('themes.show.field');

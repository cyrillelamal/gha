/* global LLMS, $ */

// = include ../vendor/js.cookie.js

/**
 * Create a no conflict reference to JS Cookies.
 *
 * @type {Object}
 */
LLMS.CookieStore = Cookies.noConflict();

/**
 * Store information in Local Storage by group.
 *
 * @since 3.36.0
 * @since 3.37.14 Use persistent reference to JS Cookies.
 * @since 4.2.0 Set sameSite to `strict` for cookies.
 *
 * @param string group Storage group id/name.
 */
LLMS.Storage = function( group ) {

	var self = this,
		store = LLMS.CookieStore;

	/**
	 * Clear all data for the group.
	 *
	 * @since 3.36.0
	 *
	 * @return void
	 */
	this.clearAll = function() {
		store.remove( group );
	};

	/**
	 * Clear a single item from the group by key.
	 *
	 * @since 3.36.0
	 *
	 * @return obj
	 */
	this.clear = function( key ) {
		var data = self.getAll();
		delete data[ key ];
		return store.set( group, data );
	};

	/**
	 * Retrieve (and parse) all data stored for the group.
	 *
	 * @since 3.36.0
	 *
	 * @return obj
	 */
	this.getAll = function() {
		return store.getJSON( group ) || {};
	}

	/**
	 * Retrieve an item from the group by key.
	 *
	 * @since 3.36.0
	 *
	 * @param string key Item key/name.
	 * @param mixed default_val Item default value to be returned when item not found in the group.
	 * @return mixed
	 */
	this.get = function( key, default_val ) {
		var data = self.getAll();
		return data[ key ] ? data[ key ] : default_val;
	}

	/**
	 * Store an item in the group by key.
	 *
	 * @since 3.36.0
	 * @since 4.2.0 Set sameSite to `strict` for cookies.
	 *
	 * @param string key Item key name.
	 * @param mixed val Item value
	 * @return obj
	 */
	this.set = function( key, val ) {
		var data = self.getAll();
		data[ key ] = val;
		return store.set( group, data, { sameSite: 'strict' } );
	};

}

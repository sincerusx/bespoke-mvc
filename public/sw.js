console.log( 'sw' );

var dataCacheName = 'appData-v1';
var cacheName     = 'appPWA-test';
var filesToCache  = [
	'/manifest.json',
	'/favicon.ico',
	'/assets/css/bootstrap.min.css',
	'/assets/css/fonts.css',
	'/assets/css/main.css',
	'/assets/js/master.js',
	'/assets/img/screenshot.jpg',
	'/assets/video/bgvideo.mp4',
	'/assets/video/bgvideo.webm'
];

// console.log('%c [ServiceWorker] All files are cached.', 'color: #20f700');

// Installing service worker
self.addEventListener( 'install', function( event ) {
	console.log( '[ServiceWorker] Performing installation.' );
	// waitUntil method extends the lifetime of an event
	event.waitUntil(
		// Open Cache
		caches.open( cacheName ).then( function( cache ) {
			// Add files to cache
			console.log( '[ServiceWorker] Caching important assets.' );
			return cache.addAll( filesToCache ).then( function() {
				// Caching success :D
				console.log( '%c [ServiceWorker] All files are cached.', 'color: #20f700' );
			} )
		} ).catch( function( err ) {
			// Caching failure :(
			console.log( '%c[ServiceWorker] Error occurred while caching. ', 'color: #f70000', err );
		} )
	);
} );

// Update service worker
self.addEventListener( 'activate', function( event ) {
	console.log( '[ServiceWorker] Activated' );
	//Delete unwanted and old caches here
	event.waitUntil(
		caches.keys().then( function( keyList ) {
			return Promise.all( keyList.map( function( key ) {
				if ( key !== cacheName && key !== dataCacheName ) {
					// Deleting the cache
					console.log( '%c [ServiceWorker] Removing old cache. ', key, 'background: #222; color: #db764e' );
					return caches.delete( key );
				}
			} ) );
		} )
	);
	/*
	 * Fixes a corner case in which the app wasn't returning the latest data.
	 * You can reproduce the corner case by commenting out the line below and
	 * then doing the following steps: 1) load app for first time so that the
	 * initial New York City data is shown 2) press the refresh button on the
	 * app 3) go offline 4) reload the app. You expect to see the newer NYC
	 * data, but you actually see the initial data. This happens because the
	 * service worker is not yet activated. The code below essentially lets
	 * you activate the service worker faster.
	 */
	return self.clients.claim();
} );

self.addEventListener( 'fetch', function( event ) {
	// console.log('[Service Worker] Fetch', event.request.url);
	/*
	var dataUrl = 'https://query.yahooapis.com/v1/public/yql';
	if (event.request.url.indexOf(dataUrl) > -1) {
		//
		// When the request URL contains dataUrl, the app is asking for fresh
		// weather data. In this case, the service worker always goes to the
		// network and then caches the response. This is called the "Cache then
		// network" strategy:
		// https://jakearchibald.com/2014/offline-cookbook/#cache-then-network
		//
		event.respondWith(
			caches.open(dataCacheName).then(function(cache) {
				return fetch(e.request).then(function(response){
					cache.put(e.request.url, response.clone());
					return response;
				});
			})
		);
	} else {
		//
		//  The app is asking for app shell files. In this scenario the app uses the
		//  "Cache, falling back to the network" offline strategy:
		//  https://jakearchibald.com/2014/offline-cookbook/#cache-falling-back-to-network
		//
		e.respondWith(
			caches.match(e.request).then(function(response) {
				return response || fetch(e.request);
			})
		);
	}
	*/

	// https://jakearchibald.com/2014/offline-cookbook

	// Cache, falling back to the network, ideal for offline first app
	/*event.respondWith(
		caches.match(event.request).then(function(response){
			return response || fetch(event.request);
		})
	);*/

	/*
	// Cache and network race, ideal for small assets when chasing performance
	function promiseAny(promises) {
		return new Promise((resolve, reject) => {
			// make sure promises are all promises
			promises = promises.map(p => Promise.resolve(p));
			// resolve this promise as soon as one resolves
			promises.forEach(
				p => p.then(resolve)
			);
			// reject if all promises reject
			promises.reduce(
				(a, b) => a.catch(() => b)
			)
			.catch(
				() => reject(Error("All failed"))
			);
		});
	};
	event.respondWith(
		promiseAny([
			caches.match(event.request),
			fetch(event.request)
		])
	);
	*/

} );
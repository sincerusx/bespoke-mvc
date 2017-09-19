// If service worker is supported, then register it
if ( 'serviceWorker' in navigator ) {
	window.addEventListener( 'load', function() {
		navigator.serviceWorker.register( '/sw.js', {
			// Scope of the service worker
			scope : './'
		} ).then( function( registration ) {
			// Registration was successful :D
			console.log( '%c [ServiceWorker] registration successful within scope: ', 'color: #20f700', registration.scope );

		}, function( err ) {
			// registration failed :(
			console.log( '%c [ServiceWorker] registration failed: ', err, 'color: #f70000' );

		} );
		navigator.serviceWorker.ready.then( function() {
			console.log( '%c [ServiceWorker] Ready', 'color: #20f700' );
		} );
		// Query current usage and availability in Temporary storage:
		/*navigator.storageQuota.queryInfo("temporary").then( function(storageInfo) {
				console.log('Quota in bytes: ' + storageInfo.quota );
				console.log('Used data in bytes: ' + storageInfo.usage);
				// Continue to initialize local cache using the obtained
				// usage and remaining space (quota - usage) information.
				initializeCache(storageInfo.usage,
					storageInfo.quota - storageInfo.usage);
			}
		);*/

		// Persisence
		/*navigator.storage.requestPersistent().then( function( granted ){
			if( granted ) {
				console.log( granted );
			}
		});*/
	} );
}
else {
	// ServiceWorker not supported :/
	console.log( '%c sw not supported. ', 'background: #222; color: #db764e' );
}
if (typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}

var magestore = magestore || {};
magestore.storepickup = magestore.storepickup || {};

magestore.storepickup.Quicksort = (function() {
	function swap(array, indexA, indexB) {
		var temp = array[indexA];
		array[indexA] = array[indexB];
		array[indexB] = temp;
	}
	function partition(array, pivot, left, right, attr) {

		var storeIndex = left,
			pivotValue = array[pivot];
		swap(array, pivot, right);
		for (var v = left; v < right; v++) {

			if (array[v][attr] < pivotValue[attr]) {
				swap(array, v, storeIndex);
				storeIndex++;
			}
		}
		swap(array, right, storeIndex);
		return storeIndex;
	}
	function sort(array, left, right, attr) {
		var pivot = null;
		if (typeof left !== 'number') {
			left = 0;
		}
		if (typeof right !== 'number') {
			right = array.length - 1;
		}
		if (left < right) {
			pivot = left + Math.ceil((right - left) * 0.5);
			newPivot = partition(array, pivot, left, right, attr);
			sort(array, left, newPivot - 1, attr);
			sort(array, newPivot + 1, right, attr);
		}
	}
	return {
		sort: sort
	};

})();

magestore.storepickup.GoogleMapManager = Class.create({
	initialize: function(mapId, options, callback) {
		this._listeners = {};
		/**
		 * set default property
		 */
		Object.extend(this, {
			mapId: mapId,
			options: {},
			callback: callback,
			map: null,
			geocoder: null,
			infoWindow: null,
			currentSystemValues: {},
			listStore: [],
			circle: null,
			circleOptions: {
				center: null,
				map: null,
				radius: 0,
				strokeColor: "#FF0000",
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: "#B9D3EE",
				fillOpacity: 0.35
			},
			storageUnit: {
				m: {
					label: 'M',
					factor: 1
				},
				km: {
					label: 'Km',
					factor: 1000
				},
				mi: {
					label: 'Mi',
					factor: 1609.34
				}
			},
			dayInWeek: ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"],
			fadeEffecting: null,
			fadeEffectTimeout: null,
			filterStoreByAreaTimeout: null,
			searchByDistanceTimeout: null
		});
		this.options = Object.extend({
			mapOptions: {},
			storeOptions: {},
			listStoreJson: [],
			maxRadius: 2000,
			currentUnit: 'Km',
			unitSelectId: '',
			allowRenderListStoreBox: true,
			searchByDistanceInputId: '',
			searchByDistanceButtonId: '',
			resetSearchByDistanceButtonId: '',
			searchOptionBoxId: '',
			searchMultiSelectOptionId: '',
			imageFullScreenMapSrc: '',
			geoYourLocationBtnId: '',
			searchByDateButtonId: '',
			searchByDateInputId: '',
			resetSearchByDateBtnId: '',
			isCheckoutPage: false
		}, options || {});

		//bind as event listener function
		this.initializeGMap = this.initializeGMap.bindAsEventListener(this);
		this.searchStoresByDistance = this.searchStoresByDistance.bindAsEventListener(this);
		this.changeRadiusBarCallback = this.changeRadiusBarCallback.bindAsEventListener(this);
		this.filterStoreByDistance = this.filterStoreByDistance.bindAsEventListener(this);
		this.refreshAllMaker = this.refreshAllMaker.bindAsEventListener(this);
		this.fullMapCallback = this.fullMapCallback.bindAsEventListener(this);
		this.searchByAreaKeyupCallback = this.searchByAreaKeyupCallback.bindAsEventListener(this);
		this.searchByDistanceKeyupCallback = this.searchByDistanceKeyupCallback.bindAsEventListener(this);
		this.filterStoreByArea = this.filterStoreByArea.bindAsEventListener(this);
		this.filterStoreByAreaCallBack = this.filterStoreByAreaCallBack.bindAsEventListener(this);
		this.geoYourLocation = this.geoYourLocation.bindAsEventListener(this);
		this.setSlider = this.setSlider.bindAsEventListener(this);

		// bind function
		this.handleResultGeocoder = this.handleResultGeocoder.bind(this);
		Event.observe(window, 'load', this.initializeGMap);
	},
	initializeGMap: function() {
		this.options.mapOptions = Object.extend({
			zoom: 4,
			center: {
				latitude: 54.8,
				longitude: -4.6
			},
			minZoom: 2,
			maxZoom: 20,
			mapTypeControl: true,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
				//                position: google.maps.ControlPosition.LEFT_CENTER
			},
			zoomControl: false,
			scaleControl: true,
			streetViewControl: false,
			// streetViewControlOptions: {
			//     position: google.maps.ControlPosition.RIGHT_TOP
			// },
			panControl: false

		}, options.mapOptions || {});

		var mapOptions = this.options.mapOptions;
		mapOptions.center = new google.maps.LatLng(mapOptions.center.latitude, mapOptions.center.longitude);
		this.map = new google.maps.Map(this.getMapElement(), mapOptions);
		this.bounds = new google.maps.LatLngBounds();
		var mcOptions = {
			gridSize: 10,
			maxZoom: 15
		};
		this.markerClusterer = new MarkerClusterer(this.map, [], mcOptions);

		this.geocoder = new google.maps.Geocoder();
		this.infoWindow = new google.maps.InfoWindow({
			maxWidth: 250,
			disableAutoPan: true
		});
		this.circle = new google.maps.Circle(this.circleOptions);

		this.autocomplete = null;
		this.directionsService = new google.maps.DirectionsService();
		this.directionsRenderer = new google.maps.DirectionsRenderer({
			draggable: true
		});

		this.requestDirections = {};

		this.indexStore = 0;

		// Your location
		this.infowindowYourLocation = new google.maps.InfoWindow();
		this.markerYourLocation = new google.maps.Marker();

		//Add search box to map
		this.ismobile = true;
		if (this.ismobile) {
			if ($('search_box')) {
				this.map.controls[google.maps.ControlPosition.LEFT_TOP].push($('search_box'));
			}
			this.options.searchByDistanceInputId = 'mb-search-distance-inp';
			this.options.searchByAreaInputId = 'mb-search-area-inp';
			this.options.radiusBarId = 'search_radius1';
		} else {
			if ($('search_box')) {
				$('search_box').hide();
			}
			if ($('store_tabs')) {
				this.map.controls[google.maps.ControlPosition.LEFT_TOP].push($('store_tabs'));
			}
			if ($('store_content')) {
				this.map.controls[google.maps.ControlPosition.LEFT_TOP].push($('store_content'));
			}
		}

		//Search by distance input box
		if ($(this.options.searchByDistanceInputId)) {
			this.searchBox = new google.maps.places.SearchBox($(this.options.searchByDistanceInputId));
			this.gmapAddListener(this.searchBox, 'places_changed', this.searchStoresByDistance);
			this.gmapAddDomListener($(this.options.searchByDistanceInputId), 'keyup', this.searchByDistanceKeyupCallback);
		}
		//Search by Distance button
		if ($(this.options.searchByDistanceButtonId)) {
			this.gmapAddDomListener($(this.options.searchByDistanceButtonId), 'click', this.searchStoresByDistance);
		}
		//Reset search by Distance button
		if ($(this.options.resetSearchByDistanceButtonId)) {
			this.gmapAddDomListener($(this.options.resetSearchByDistanceButtonId), 'click', this.refreshAllMaker);
		}

		if ($(this.options.searchByAreaInputId)) {
			this.gmapAddDomListener($(this.options.searchByAreaInputId), 'keyup', this.searchByAreaKeyupCallback);
		}

		$$('.list-area input[type=checkbox]').each((function(checkbox) {
			this.gmapAddDomListener(checkbox, 'click', this.filterStoreByAreaCallBack);
		}).bind(this));

		//Search by Area button
		if ($(this.options.searchByAreaButtonId)) {
			this.gmapAddDomListener($(this.options.searchByAreaButtonId), 'click', this.filterStoreByArea);
		}

		//Reset search by Area button
		if ($(this.options.resetSearchByAreaButtonId)) {
			this.gmapAddDomListener($(this.options.resetSearchByAreaButtonId), 'click', this.refreshAllMaker);
		}

		//Event when change value of radius bar



		this.divRangeSlider = document.createElement('div');
		this.divRangeSlider.id = 'range-slider-input';
		this.divRangeSlider.addClassName('range-slider-input');
		this.divRangeSlider.innerHTML = '<div class="handle-slider"></div>';

		if ($(this.options.searchByDistanceInputId)) {
			$(this.options.searchByDistanceInputId).insert({
				after: this.divRangeSlider
			});
		}
		if (this.options.isCheckoutPage) {
			if (this.getBrowserAgent() === 'safari' && $(this.options.searchByDistanceInputId)) {
				google.maps.event.addDomListenerOnce($(this.options.searchByDistanceInputId), 'focus', this.setSlider);
			} else {
				google.maps.event.addListenerOnce(this.map, 'resize', this.setSlider);
			}
		} else {
			this.setSlider();
		}

		/* Add geo your location button*/
		if ($(this.options.geoYourLocationBtnId)) {
			this.gmapAddDomListener($(this.options.geoYourLocationBtnId), 'click', this.geoYourLocation);
		}

		/* Event for custom zoom in control  */
		if ($('widget-zoom-in')) {
			this.gmapAddDomListener($('widget-zoom-in'), 'click', (function() {
				this.map.setZoom(this.map.getZoom() + 1);
				if (this.map.getStreetView().getVisible()) {
					this.map.getStreetView().setZoom(this.map.getStreetView().getZoom() + 1);
				}
			}).bindAsEventListener(this));
		}

		/* Event for custom zoom out control  */
		if ($('widget-zoom-out')) {
			this.gmapAddDomListener($('widget-zoom-out'), 'click', (function() {
				this.map.setZoom(this.map.getZoom() - 1);
				if (this.map.getStreetView().getVisible()) {
					this.map.getStreetView().setZoom(this.map.getStreetView().getZoom() - 1);
				}
			}).bindAsEventListener(this));
		}


		if ($('search-tooltip')) {
			this.map.controls[google.maps.ControlPosition.LEFT_TOP].push($('search-tooltip'));
		}

		/**
		 * button full screen map
		 */
		this.btnFullScreenMap = $('full-screen-map-btn');
		this.btnFullScreenMap.title = translateJson.fullMapTitle;
		if (this.btnFullScreenMap) {
			this.gmapAddDomListener($('full-screen-map-btn'), 'click', this.fullMapCallback);
		}

		this.fireCustomEvent({
			type: 'goolgemap:initMap',
			gmap: this
		});

		this.initListStore();
		// this.refreshAllMaker();
		// this.renderListStoreToHtml(this.listStore);
	},
	getNextIndex: function() {
		if (this.indexStore < this.listStore.length - 1) {
			this.indexStore++;
		}
		return this.indexStore;
	},
	getPrevIndex: function() {
		if (this.indexStore > 0) {
			this.indexStore--;
		}
		return this.indexStore;
	},
	getBrowserAgent: function() {
		try {

			var ua = navigator.userAgent,
				tem,
				M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
			if (/trident/i.test(M[1])) {
				tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
				return 'IE ' + (tem[1] || '');
			}
			if (M[1] === 'Chrome') {
				tem = ua.match(/\bOPR\/(\d+)/)
				if (tem != null) {
					return 'Opera ' + tem[1];
				}
			}
			M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
			if ((tem = ua.match(/version\/(\d+)/i)) != null) {
				M.splice(1, 1, tem[1]);
			}
			return M[0].toLowerCase();
		} catch (e) {
			return '';
		}
	},
	setSlider: function() {
		try {
			this.rangeSlider = new Control.Slider(this.divRangeSlider.down('.handle-slider'), this.divRangeSlider, {
				range: $R(1, this.options.maxRadius),
				sliderValue: this.options.maxRadius,
				onSlide: (function(value) {
					this.updateRadiusLabel('range-slider-label', value);
					this.changeRadiusBarCallback();
				}).bindAsEventListener(this),
				onChange: (function(value) {
					this.updateRadiusLabel('range-slider-label', value);
					this.changeRadiusBarCallback();
				}).bindAsEventListener(this)
			});
			this.updateRadiusLabel('range-slider-label', this.rangeSlider.getRange().start);
		} catch (e) {
//			console.log(e.name + ' : ' + e.message);
//			console.trace();
		}
	},
	getMapElement: function() {
		return $(this.mapId);
	},
	gmapAddDomListener: function(element, eventName, callback) {
		try {
			google.maps.event.addDomListener(element, eventName, callback);
		} catch (e) {
//			console.log(e.name + ' : ' + e.message);
//			console.trace();
		}
	},
	gmapAddListener: function(element, eventName, callback) {
		try {
			google.maps.event.addListener(element, eventName, callback);
		} catch (e) {
//			console.log(e.name + ' : ' + e.message);
//			console.trace();
		}
	},
	/* add listener for custom event */
	addListener: function(type, listener) {
		if (typeof this._listeners[type] === "undefined") {
			this._listeners[type] = [];
		}

		this._listeners[type].push(listener);
	},
	/* fire event for custom event */
	fireCustomEvent: function(event) {
		if (typeof event === "string") {
			event = {
				type: event
			};
		}
		if (!event.target) {
			event.target = this;
		}

		if (!event.type) { //falsy
			throw new Error("Event object missing 'type' property.");
		}

		if (this._listeners[event.type] instanceof Array) {
			var listeners = this._listeners[event.type];
			for (var i = 0, len = listeners.length; i < len; i++) {
				listeners[i].call(this, event);
			}
		}
	},
	fitScrollBar: function() {
		if ($$('.widget-runway-tray-wrapper')[0]) {
			if ($$('.widget-runway-tray-wrapper')[0].scrollWidth > $$('.widget-runway-tray-wrapper')[0].clientWidth) {
				$$('.widget-runway-card-caption-wrapper').invoke('addClassName', 'fitScroll');
			} else {
				$$('.widget-runway-card-caption-wrapper').invoke('removeClassName', 'fitScroll');
			}
		}
	},
	/* remove listener of custom event*/
	removeListener: function(type, listener) {
		if (this._listeners[type] instanceof Array) {
			var listeners = this._listeners[type];
			for (var i = 0, len = listeners.length; i < len; i++) {
				if (listeners[i] === listener) {
					listeners.splice(i, 1);
					break;
				}
			}
		}
	},
	setCurrentUnit: function(unit) {
		this.options.currentUnit = unit;
	},
	setMapStyle: function(style) {
		this.beforeChangeStyre = {};
		for (var fieldStyle in style) {
			this.beforeChangeStyre[fieldStyle] = this.getMapElement().getStyle(fieldStyle);
		}
		this.getMapElement().setStyle(style);
	},
	restoreLastChangeMapStyle: function() {
		if (this.beforeChangeStyre) {
			this.setMapStyle(this.beforeChangeStyre);
		}
	},
	fullMapCallback: function() {
		if (!$('store-pickup-map-box')) {
			return;
		}



		if ($('store-pickup-map-box').hasClassName('mapfull')) {
			$('store-pickup-map-box').removeClassName('mapfull');

			if ($('app-viewcard-strip')) {
				$('app-viewcard-strip').removeClassName('zero-padding');
			}
			// if (document.viewport.getHeight() < 600 && this.options.isCheckoutPage) {
			//     $('app-viewcard-strip').setStyle({
			//         bottom: '0px'
			//     });
			// }

			this.btnFullScreenMap.down('.full-screen-map-btn-icon-common').addClassName('expland').removeClassName('contract');
			this.btnFullScreenMap.title = translateJson.fullMapTitle;

			this.restoreLastChangeMapStyle();
		} else {
			$('store-pickup-map-box').addClassName('mapfull');

			if ($('app-viewcard-strip')) {
				$('app-viewcard-strip').addClassName('zero-padding');
			}

			// if (document.viewport.getHeight() < 600 && this.options.isCheckoutPage) {
			//     $('app-viewcard-strip').setStyle({
			//         bottom: '170px'
			//     });
			// }

			this.btnFullScreenMap.down('.full-screen-map-btn-icon-common').addClassName('contract').removeClassName('expland');
			this.btnFullScreenMap.title = translateJson.normalMapTitle;

			this.setMapStyle({
				height: $('store-pickup-map-box').getHeight() + 'px'
			});
		}
		this.mapTriggerResize();
		this.mapTriggerResize();
	},
	mapTriggerResize: function() {
		google.maps.event.trigger(this.map, 'resize');
	},
	updateRadiusLabel: function(labelId, value) {
		if ($(labelId)) {
			$(labelId).update(Math.round(value) + ' ' + this.storageUnit[this.options.currentUnit].label);
		}
	},
	filterStoreByDistance: function() {
		this.markerClusterer.clearMarkers();
		var circleCenter = this.circle.getCenter();
		var circleRadius = this.circle.getRadius();
		var map = this.circle.getMap();

		if (map === null) {
			return this;
		}

		this.listStore.invoke('getDistance', circleCenter);

		//Sort store by distance
		magestore.storepickup.Quicksort.sort(this.listStore, 0, this.listStore.length - 1, 'distance');

		this.renderListStoreToHtml(this.listStore);

		var i = 0;
		while (i < this.listStore.length && this.listStore[i].distance <= circleRadius) {
			this.markerClusterer.addMarker(this.listStore[i].getMarker());
			i++;
		}

		this.indexStore = i;
		for (var j = i; j < this.listStore.length; j++) {
			this.listStore[j].hide();
		};

		this.fitScrollBar();

		this.updateMessage(translateJson.numberStore + this.countStoreInMap());
		this.map.setZoom(Math.round(14 - Math.log(this.getRadiusBarValue() / 1000) / Math.LN2));
		this.map.setCenter(circleCenter);
	},
	searchStoresByDistance: function() {
		if (this.searchByDistanceTimeout !== null) {
			clearTimeout(this.searchByDistanceTimeout);
			this.searchByDistanceTimeout = null;
		}
		if ($(this.options.searchByDistanceInputId).value.length === 0) {
			return;
		}
		this.clearSomeThing();
		this.geocoder.geocode({
			'address': $(this.options.searchByDistanceInputId).value
		}, this.handleResultGeocoder);
	},
	handleResultGeocoder: function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			this.map.setCenter(results[0].geometry.location);
			this.circle.setMap(this.map);
			this.circle.setCenter(this.map.getCenter());
			this.circle.setRadius(this.getRadiusBarValue());
			this.filterStoreByDistance();
		}
	},
	changeRadiusBarCallback: function() {
		if (this.circle && this.circle.getMap()) {
			var radiusBarValue = this.getRadiusBarValue();
			var circleRadius = this.circle.getRadius();


			// this.listStore.invoke('changeRadiusBar', radiusBarValue, this.circle.getRadius(), this.map, this.markerClusterer);
			for (var i = 0, len = this.listStore.length; i < len; i++) {
				if (this.listStore[i].distance <= radiusBarValue && this.listStore[i].distance > circleRadius) {
					this.listStore[i].show();
					this.markerClusterer.addMarker(this.listStore[i].getMarker());
				} else if (this.listStore[i].distance >= radiusBarValue && this.listStore[i].distance < circleRadius) {
					this.listStore[i].hide();
					this.markerClusterer.removeMarker(this.listStore[i].getMarker());
				}
			}

			this.fitScrollBar();
			this.circle.setRadius(radiusBarValue);
			this.map.setCenter(this.circle.getCenter());
			this.map.setZoom(Math.round(14 - Math.log(radiusBarValue / 1000) / Math.LN2));


			var countStore = this.countStoreInMap();

			if (countStore >= 1) {
				this.updateMessage(translateJson.numberStore + countStore);
			} else {
				this.updateMessage(translateJson.storeNotFound);
			}
		}
	},
	countStoreInMap: function() {
		return this.markerClusterer.getTotalMarkers();
	},
	filterStoreByAreaCallBack: function() {
		if (this.filterStoreByAreaTimeout !== null) {
			clearTimeout(this.filterStoreByAreaTimeout);
		}
		var timeout;

		if (this.listStore.legnth >= 5000) {
			timeout = 2000;
		} else if (this.listStore.legnth >= 2000) {
			timeout = 1000;
		} else {
			timeout = 500;
		}

		this.filterStoreByAreaTimeout = setTimeout((function() {
			this.filterStoreByAreaTimeout = null;
			this.filterStoreByArea();
		}).bind(this), timeout);
	},
	filterStoreByTag: function(tagIds) {
		this.clearSomeThing();
		this.markerClusterer.clearMarkers();

		var bounds = new google.maps.LatLngBounds();
		var aStore = null;

		for (var i = 0, leni = this.listStore.length; i < leni; i++) {
			this.listStore[i].tag_ids = this.listStore[i].tag_ids || '';
			var storeTags = this.listStore[i].tag_ids.split(',');
			var isShow = false;
			for (var j = 0; j < tagIds.length; j++) {
				if (storeTags.indexOf(tagIds[j]) !== -1) {
					isShow = true;
					break;
				}
			};

			if (tagIds.length === 0) {
				isShow = true;
			}

			if (isShow) {
				aStore = this.listStore[i];
				this.markerClusterer.addMarker(this.listStore[i].getMarker());
				this.listStore[i].show();
				bounds.extend(this.listStore[i].getMarker().getPosition());
			} else {
				this.markerClusterer.removeMarker(this.listStore[i].getMarker());
				this.listStore[i].hide();
			}
		};

		this.fitScrollBar();

		var countStore = this.countStoreInMap();
		this.updateMessage(translateJson.numberStore + countStore);

		if (countStore >= 1) {
			this.map.panTo(bounds.getCenter());
			this.map.fitBounds(bounds);
			if (countStore === 1) {
				google.maps.event.trigger(aStore.getMarker(), 'click');
			}
			this.updateMessage(translateJson.numberStore + countStore);
		} else {
			this.updateMessage(translateJson.storeNotFound);
		}

	},
	filterStoreByArea: function() {
		var searchContent = $(this.options.searchByAreaInputId) ? $(this.options.searchByAreaInputId).value : '';
		searchContent = searchContent.trim().toLocaleLowerCase();

		if (searchContent === '') {
			return;
		}
		this.clearSomeThing();
		this.markerClusterer.clearMarkers();

		var searchOptions = [];
		$$('.list-area input[type=checkbox]').each(function(checkbox) {
			if (checkbox.checked) {
				searchOptions.push(checkbox.id.substr('checkbox_store_'.length));
			}
		});
		var bounds = new google.maps.LatLngBounds();
		if (searchOptions.length == 0) {
			this.updateMessage(translateJson.pleaseChoose);
			return;
		}

		this.circle.setMap(null);
		var listStoreFound = [];

		while (searchContent.search('  ') !== -1) {
			searchContent = searchContent.replace('  ', ' ');
		}
		var arrayWordsSearch = searchContent.split(' ');

		this.listStore.invoke('hide');
		this.listStore.invoke('setDisplayByArea', arrayWordsSearch, searchOptions, bounds, this.map, listStoreFound, this.markerClusterer);
		listStoreFound.invoke('show');

		this.fitScrollBar();

		if (listStoreFound.length >= 1) {
			this.map.panToBounds(bounds);
			this.map.fitBounds(bounds);
			if (listStoreFound.length === 1) {
				google.maps.event.trigger(listStoreFound.first().getMarker(), 'click');
			}
			this.updateMessage(translateJson.numberStore + listStoreFound.length);
		} else {
			this.updateMessage(translateJson.storeNotFound);
		}
	},
	searchByAreaKeyupCallback: function(event) {
		var key = event.keyCode;
		if (key == 8 || key == 46 || key == 13 || (key >= 48 && key <= 90)) {
			this.filterStoreByAreaCallBack();
		}
	},
	searchByDistanceKeyupCallback: function(event) {
		if (this.searchByDistanceTimeout !== null) {
			clearTimeout(this.searchByDistanceTimeout);
		}
		var key = event.keyCode;
		if (key == 13) {
			this.searchStoresByDistance();
		} else {
			this.searchByDistanceTimeout = setTimeout((function() {
				this.searchStoresByDistance();
			}).bind(this), 3000);
		}
	},
	refreshAllMaker: function() {
		this.clearSomeThing();
		this.markerClusterer.clearMarkers();

		if ($(this.options.searchByDistanceInputId)) {
			$(this.options.searchByDistanceInputId).value = '';
		}

		if ($(this.options.searchByAreaInputId)) {
			$(this.options.searchByAreaInputId).value = '';
		}

		if ($(this.options.searchByDateInputId)) {
			$(this.options.searchByDateInputId).value = '';
		}

		if ($(this.options.resetSearchByAreaButtonId)) {
			$(this.options.resetSearchByAreaButtonId).hide();
		}
		if ($(this.options.resetSearchByDistanceButtonId)) {
			$(this.options.resetSearchByDistanceButtonId).hide();
		}
		this.listStore.each((function(store) {
			this.markerClusterer.addMarker(store.getMarker());
		}).bind(this));

		if (this.listStore.length >= 1) {
			this.map.panToBounds(this.bounds);
			this.map.fitBounds(this.bounds);
			// if (this.listStore.length === 1) {

			//  google.maps.event.addListenerOnce(this.map, 'tilesloaded', (function() {
			//      google.maps.event.trigger(this.listStore.first().getMarker(), 'click');
			//  }).bindAsEventListener(this));
			// }
		}
		this.updateMessage(translateJson.numberStore + this.listStore.length);
		this.listStore.invoke('show');
		this.fitScrollBar();
	},
	setCurentStore: function(store) {
		this.currentSystemValues.store = store;
	},
	getCurrentStore: function() {
		return this.currentSystemValues.store;
	},
	selectStoreCheckout: function() {
		if (this.options.isCheckoutPage && $('selected_store') && this.getCurrentStore()) {
			$('selected_store').update(this.getCurrentStore().store_name);
			$('selected_store').show();
		}
	},
	clickMarkerCallBack: function(event, store) {
		this.openInfoWindow(store);
		this.selectStoreCheckout();
		if (this.map.getStreetView().getVisible()) {
			this.map.getStreetView().setVisible(false);
		}

		if (!event || event.latLng) {
			var storeClassItem = this.options.storeOptions.storeClassItem;
			var listStoreBoxId = this.options.storeOptions.listStoreBoxId;

			if ($(listStoreBoxId) && $(listStoreBoxId).up() && $(listStoreBoxId).down('.' + storeClassItem) && store.getStoreDom()) {
				var firstStore = $(listStoreBoxId).up().cumulativeScrollOffset().left + $(listStoreBoxId).down('.' + storeClassItem).cumulativeOffset().left;
				$(listStoreBoxId).up().scrollLeft += store.getStoreDom().cumulativeOffset().left - firstStore;
			}
		}

	},
	beautyInfoWindow: function() {
		var divIwContainer = this.infoWindow.getContent();

		var iwOuter = divIwContainer.up('.gm-style-iw');
		if (iwOuter) {
			iwOuter.addClassName('custom-gm-style-iw');

			var iwOuterWrapper = iwOuter.up();
			iwOuterWrapper.addClassName('width-gm-style-iw');

			var iwBackground = iwOuter.previous();
			var iwBackgroundChildElements = iwBackground.childElements();
			// Removes background shadow DIV
			iwBackgroundChildElements[1].setStyle({
				'display': 'none'
			});

			// Removes white background DIV
			iwBackgroundChildElements[3].setStyle({
				'display': 'none'
			});

			// Changes the desired tail shadow color.
			iwBackgroundChildElements[2].childElements().each(function(el) {
				el.down().setStyle({
					'box-shadow': 'rgba(72, 181, 233, 0.6) 0px 1px 6px',
					'z-index': '1'
				});
			});

			// Reference to the div that groups the close button elements.
			var iwCloseBtn = iwOuter.next();

			// Apply the desired effect to the close button
			iwCloseBtn.setStyle({
				width: '25px',
				height: '25px',
				opacity: '1',
				right: (this.getBrowserAgent().toLowerCase().search("ie") !== -1) ? '-25px' : '-12px',
				// left: (iwOuter.getWidth() - iwCloseBtn.getWidth() / 2) + 'px',
				top: '3px',
				border: '6px solid #48b5e9',
				'border-radius': '13px',
				'box-shadow': '0 0 5px #3990B9'
			});

			iwCloseBtn.observe('click', (function() {
				this.infoWindow.close();
			}).bindAsEventListener(this));

			// If the content of infowindow not exceed the set maximum height, then the gradient is removed.
			if (divIwContainer.down('.iw-content').getHeight() < 140) {
				divIwContainer.down('.iw-bottom-gradient').setStyle({
					display: 'none'
				});
			}

			// The API automatically applies 0.7 opacity to the button after the mouseout event. This function reverses this event to the desired value.
			iwCloseBtn.observe('mouseout', function() {
				this.setStyle({
					opacity: '1'
				});
			});
		}
	},
	openInfoWindow: function(store) {
		var divIwContainer = $('template-iw-container').clone(true);
		divIwContainer.id = 'iw-container';

		var template = new Template(divIwContainer.innerHTML);
		divIwContainer.innerHTML = template.evaluate(store);

		this.infoWindow.setContent(divIwContainer);
		this.infoWindow.open(this.map, store.getMarker());

		$$('.widget-runway-subview-card-view-container .widget-runway-card-background-active').invoke('removeClassName', 'widget-runway-card-background-active');

		if (store.getStoreDom() && store.getStoreDom().down('.widget-runway-card-background')) {
			store.getStoreDom().down('.widget-runway-card-background').addClassName('widget-runway-card-background-active');
		}

		// css beauty for info window
		this.beautyInfoWindow();

		if (this.getCurrentStore()) {
			// select a store

			if (this.getCurrentStore().getId() !== store.getId()) {
				// select another store



				this.setCurentStore(store);
				this.directionsRenderer.setPanel(null);
				this.directionsRenderer.setMap(null);
				this.directionsRenderer = new google.maps.DirectionsRenderer({
					draggable: true
				});

				this.map.setZoom(store.getZoomLevel());
				this.map.panTo(store.getMarker().getPosition());
			} else {
				// select current store

				if (this.directionsRenderer.getMap()) {
					this.map.panTo(store.getMarker().getPosition());
					if (this.currentSystemValues.startRouter) {
						$('originA').value = this.currentSystemValues.startRouter;
					}
				}

				if ($('directions-tms') && this.currentSystemValues.travelMode) {
					$('directions-tms').select('li.travel', 'li.travel div').invoke('removeClassName', 'active');
					var travelMode = this.currentSystemValues.travelMode;
					$('directions-tms').select('li.travel[travel="' + travelMode + '"]', '.' + travelMode.toLowerCase()).invoke('addClassName', 'active');
				}
			}
		} else {
			// first time to select a store

			this.setCurentStore(store);
			this.directionsRenderer = new google.maps.DirectionsRenderer({
				draggable: true
			});
			this.map.setZoom(store.getZoomLevel());
			this.map.panTo(store.getMarker().getPosition());
		}



		this.directionsRenderer.setPanel($('directions-panel'));

		this.autocomplete = new google.maps.places.Autocomplete($('originA'));
		this.autocomplete.bindTo('bounds', this.map);

		this.gmapAddListener(this.autocomplete, 'place_changed', (function() {
			this.calcRoute($('originA').value, store);
		}).bindAsEventListener(this));

		this.gmapAddDomListener($('originA'), 'blur', (function() {
			this.calcRoute($('originA').value, store);
		}).bindAsEventListener(this));


		$('btn-getdirections').setStyle({
			height: $('originA').getHeight() + 'px',
			'line-height': '0'
		});
		$('btn-getdirections').observe('click', (function() {
			this.calcRoute($('originA').value, store);
		}).bindAsEventListener(this));

		$('directions-tms').select('.travel').each((function(btn) {
			btn.observe('click', (function() {
				$('directions-tms').select('.travel', '.travel div').invoke('removeClassName', 'active');
				btn.addClassName('active');
				btn.down().addClassName('active');
				this.calcRoute($('originA').value, store);
			}).bindAsEventListener(this));
		}).bind(this));

	},
	initListStore: function() {
		this.clearSomeThing();

		$A(this.options.listStoreJson).each((function(storeJson) {

			var store = new magestore.storepickup.Store(storeJson, this.options.storeOptions);
			this.listStore.push(store);

			this.markerClusterer.addMarker(store.getMarker());

			this.gmapAddListener(store.getMarker(), 'click', this.clickMarkerCallBack.bindAsEventListener(this, store));
			this.bounds.extend(store.getMarker().getPosition());

			if ($(this.options.storeOptions.listStoreBoxId)) {
				$(this.options.storeOptions.listStoreBoxId).insert(store.toHtml());
				if (store.getStoreDom()) {
					Event.observe(store.getStoreDom(), 'click', this.clickMarkerCallBack.bindAsEventListener(this, store))
				}
			}

		}).bind(this));

		if (this.listStore.length >= 1) {
			this.map.panToBounds(this.bounds);
			this.map.fitBounds(this.bounds);
			if (this.listStore.length === 1) {

				google.maps.event.addListenerOnce(this.map, 'tilesloaded', (function() {
					this.map.setZoom(this.listStore.first().getZoomLevel());
					this.map.panTo(this.listStore.first().getMarker().getPosition());
					google.maps.event.trigger(this.listStore.first().getMarker(), 'click');
				}).bindAsEventListener(this));
			}
		}

		this.updateMessage(translateJson.numberStore + this.listStore.length);
		this.fitScrollBar();
		this.options.listStoreJson = null;
	},
	clearMarkers: function() {
		this.listStore.invoke('setMarkerMap', null);
		this.listStore.invoke('clearListeners', 'click');
	},
	clearSomeThing: function() {
		this.updateDirectionsPanel('');

		this.directionsRenderer.setMap(null);
		this.directionsRenderer.setPanel(null);

		this.infoWindow.close();
		this.circle.setMap(null);
		this.markerYourLocation.setMap(null);
		this.infowindowYourLocation.setMap(null);
		this.setCurentStore(null);
		this.currentSystemValues.travelMode = null;
		this.currentSystemValues.startRouter = null;
		this.currentSystemValues.responseDirections = null;

		if (this.options.isCheckoutPage && $('selected_store')) {
			$('selected_store').update("");
			$('selected_store').show();
		}
	},
	getRadiusBarValue: function() {
		var radiusbarValue = this.rangeSlider.getRange().start;
		return radiusbarValue * this.storageUnit[this.options.currentUnit].factor;
	},
	calcRoute: function(start, store) {
		this.updateDirectionsPanel('');
		this.directionsRenderer.setMap(null);
		this.currentSystemValues.startRouter = start;
		if (typeof start === 'string' && start.trim() === '') {
			return;
		}

		var travelMode = '';
		if ($('directions-tms') && $('directions-tms').select('.travel.active').first()) {
			travelMode = $('directions-tms').select('.travel.active').first().getAttribute('travel');
		}
		this.currentSystemValues.travelMode = travelMode;

		var end = store.getMarker().getPosition();

		this.requestDirections = {
			origin: start,
			destination: end,
			travelMode: google.maps.TravelMode[travelMode]
		};

		this.directionsService.route(this.requestDirections, (function(response, status) {

			switch (status) {
				case google.maps.DirectionsStatus.OK:
					this.directionsRenderer.setMap(this.map);
					this.directionsRenderer.setDirections(response);
					this.currentSystemValues.responseDirections = response;
					break;
				case google.maps.DirectionsStatus.NOT_FOUND:
					this.updateDirectionsPanel(translateJson.direction[0]);
					break;
				case google.maps.DirectionsStatus.OVER_QUERY_LIMIT:
					this.updateDirectionsPanel(translateJson.direction[1]);
					break;
				case google.maps.DirectionsStatus.REQUEST_DENIED:
					this.updateDirectionsPanel(translateJson.direction[2]);
					break;
				case google.maps.DirectionsStatus.UNKNOWN_ERROR:
					this.updateDirectionsPanel(translateJson.direction[3]);
					break;
				case google.maps.DirectionsStatus.ZERO_RESULTS:
					this.updateDirectionsPanel(translateJson.direction[4]);
					break;
				case google.maps.DirectionsStatus.REQUEST_DENIED:
					this.updateDirectionsPanel(translateJson.direction[5]);
					break;
			}
			if (status !== google.maps.DirectionsStatus.OK) {
				this.currentSystemValues.responseDirections = null;
				if (this.getCurrentStore() && this.markerYourLocation.getMap()) {
					var bounds = new google.maps.LatLngBounds();
					bounds.extend(this.getCurrentStore().getMarker().getPosition());
					bounds.extend(this.markerYourLocation.getPosition());
					this.map.fitBounds(bounds);
				}
			}
		}).bind(this));

	},
	updateDirectionsPanel: function(message) {
		if ($('directions-panel')) {
			$('directions-panel').update(message);
		}
	},
	handleGeoYourLocation: function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			var address = "",
				city = "",
				state = "",
				zipcode = "",
				country = "",
				formattedAddress = "";
			var lat;
			var lng;

			for (var i = 0, len = results[0].address_components.length; i < len; i++) {
				var addr = results[0].address_components[i];
				// check if this entry in address_components has a type of country
				if (addr.types[0] == 'country')
					country = addr.long_name;
				else if (addr.types[0] == 'street_address') // address 1
					address = address + addr.long_name;
				else if (addr.types[0] == 'establishment')
					address = address + addr.long_name;
				else if (addr.types[0] == 'route') // address 2
					address = address + addr.long_name;
				else if (addr.types[0] == 'postal_code') // Zip
					zipcode = addr.short_name;
				else if (addr.types[0] == ['administrative_area_level_1']) // State
					state = addr.long_name;
				else if (addr.types[0] == ['locality']) // City
					city = addr.long_name;
			}

			if (results[0].formatted_address != null) {
				formattedAddress = results[0].formatted_address;
			}

			//debugger;

			var location = results[0].geometry.location;

			lat = location.lat();
			lng = location.lng();

			var contentObject = {
				formattedAddress: formattedAddress,
				city: city,
				state: state,
				country: country
			};
			var content = '';
			if ($('infoyourlocation')) {
				var dom = $('infoyourlocation').clone(true);
				var template = new Template(dom.innerHTML);
				dom.innerHTML = template.evaluate(contentObject);
				content = dom;
			} else {
				content = 'City: ' + city + '\n' + 'State: ' + state + '\n' + 'Zip: ' + zipcode + '\n' + 'Formatted Address: ' + formattedAddress + '\n' + 'Lat: ' + lat + '\n' + 'Lng: ' + lng;
			}

			this.markerYourLocation.setMap(this.map);
			this.infowindowYourLocation.open(this.map, this.markerYourLocation);
			this.infowindowYourLocation.setContent(content);

			this.gmapAddDomListener(this.markerYourLocation, 'click', (function() {
				this.infowindowYourLocation.open(this.map, this.markerYourLocation);
				this.infowindowYourLocation.setContent(content);
				this.map.setCenter(this.markerYourLocation.getPosition());
				this.map.setZoom(17);
			}).bindAsEventListener(this));

			if (this.getCurrentStore()) {
				this.openInfoWindow(this.getCurrentStore());
				if ($('originA')) {
					$('originA').value = formattedAddress;
				}
				this.calcRoute(formattedAddress, this.getCurrentStore());
				$$('a[aria-controls="tab-directions"]').invoke('click');
			} else {
				this.map.setCenter(this.markerYourLocation.getPosition());
				this.map.setZoom(17);
			}
		}
	},
	geoYourLocation: function() {
		if (this.map.getStreetView().getVisible()) {
			this.map.getStreetView().setVisible(false);
		}
		var options = {
			map: this.map,
			position: new google.maps.LatLng(60, 105),
		};
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition((function(position) {
				this.markerYourLocation.setMap(this.map);
				this.markerYourLocation.setPosition(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({
					latLng: this.markerYourLocation.getPosition()
				}, this.handleGeoYourLocation.bind(this));

			}).bind(this), (function() {
				options.content = 'Error: The Geolocation service failed.';
				var infowindow = new google.maps.InfoWindow(options);
				this.map.setCenter(options.position);
				this.map.setZoom(17);
			}).bind(this));
		} else {
			// Browser doesn't support Geolocation
			options.content = 'Error: Your browser does not support geolocation.';
			var infowindow = new google.maps.InfoWindow(options);
			this.map.setCenter(options.position);
			this.map.setZoom(17);
		}
	},
	updateMessage: function(message) {
		if ($('search-tooltip')) {
			$('search-tooltip').update(message);
			if (this.fadeEffectTimeout === null) {
				$('search-tooltip').show();
				fadeEffect.init('search-tooltip', 1);
			} else {
				clearTimeout(this.fadeEffectTimeout);
			}
			this.fadeEffectTimeout = setTimeout((function() {
				fadeEffect.init('search-tooltip', 0);
				this.fadeEffectTimeout = null;
				$('search-tooltip').hide();
			}).bind(this), 5000);
		}
	},
	renderListStoreToHtml: function(listStore) {
		if ($(this.options.storeOptions.listStoreBoxId)) {
			$(this.options.storeOptions.listStoreBoxId).innerHTML = '';
			$A(listStore).each((function(store) {
				$(this.options.storeOptions.listStoreBoxId).insert(store.toHtml());
				this.gmapAddDomListener(store.getStoreDom(), 'click', this.clickMarkerCallBack.bindAsEventListener(this, store));
			}).bind(this));
		}
	}
});

magestore.storepickup.Store = Class.create();
magestore.storepickup.Store.prototype = {
	initialize: function(store, storeOptions) {
		Object.extend(this, store || {});
		Object.extend(this, {
			map: null,
			infoWindow: null,
			marker: null,
			distance: null,
			URL_ICON: '',
			zoom_default: 16
		});

		this.storeOptions = Object.extend({
			imageMarkerIcon: '',
			storeIdItem: 'store-item',
			storeClassItem: 'store-item',
			listStoreBoxId: '',
			storeTemplateId: '',
		}, storeOptions || {});

		if (this.image_icon) {
			this.URL_ICON = this.storeOptions.imageMarkerIcon.replace('{id}', this.store_id);
			this.URL_ICON = this.URL_ICON.replace('{icon}', this.image_icon);
		} else if (this.pin_color && this.pin_color != 'f75448') {
			this.URL_ICON = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + this.pin_color;
		}
		this.createMarker();
	},
	getId: function() {
		return this.store_id;
	},
	setMarkerMap: function(map) {
		if (this.marker) {
			this.marker.setMap(map);
		}
	},
	addToCluster: function(markerClusterer) {
		if (markerClusterer instanceof MarkerClusterer) {
			markerClusterer.addMarker(this.getMarker());
		}
	},
	removeFromCluster: function(markerClusterer) {
		if (markerClusterer instanceof MarkerClusterer) {
			markerClusterer.removeMarker(this.getMarker());
		}
	},
	getMarker: function() {
		return this.marker;
	},
	getStoreBoxId: function() {
		return this.storeOptions.storeIdItem + '-' + (this.store_id || '');
	},
	getStoreDom: function() {
		return $(this.getStoreBoxId());
	},
	getZoomLevel: function() {
		if (typeof this.zoom_level !== 'undefined' && this.zoom_level !== null && isNaN(parseFloat(this.zoom_level) === false)) {
			if (this.zoom_level < 2 || this.zoom_level > 20) {
				return this.zoom_default;
			}
			return parseFloat(this.zoom_level);
		}
		return this.zoom_default;
	},
	setMarker: function(marker) {
		this.marker = marker;
	},
	checkVar: function(v) {
		return typeof v !== "undefined" && v !== null;
	},
	createMarker: function() {
		try {
			if (this.checkVar(this.store_latitude) && this.checkVar(this.store_longitude)) {
				this.marker = new google.maps.Marker({
					position: new google.maps.LatLng(this.store_latitude, this.store_longitude),
					icon: this.URL_ICON
				});
			} else {
				throw new Error('Invalid latitude,longitude of store ' + this.store_id);
			}
		} catch (e) {
//			console.log(e.name + ' : ' + e.message);
		}
	},
	clearListeners: function(eventName) {
		try {
			if (this.checkVar(eventName)) {
				google.maps.event.clearListeners(this.marker, eventName);
			} else {
				google.maps.event.clearListeners(this.marker);
			}
		} catch (e) {
//			console.log(e.name + ' : ' + e.message);
		}
	},
	show: function() {
		if ($(this.getStoreBoxId())) {
			$(this.getStoreBoxId()).show();
		}
	},
	hide: function() {
		if ($(this.getStoreBoxId())) {
			$(this.getStoreBoxId()).hide();
		}
	},
	setDisplayInCirle: function(circle, map) {
		if (this.distance !== null && this.distance <= circle.getRadius()) {
			this.show();
			this.setMarkerMap(map);
		} else {
			this.hide();
			this.setMarkerMap(null);
		}
	},
	changeRadiusBar: function(radiusBarValue, circleRadius, map, markerClusterer) {
		if (this.distance <= radiusBarValue && this.distance > circleRadius) {
			this.show();
			// this.setMarkerMap(map);
			markerClusterer.addMarker(this.marker);
		} else if (this.distance >= radiusBarValue && this.distance < circleRadius) {
			this.hide();
			// this.setMarkerMap(null);
			markerClusterer.removeMarker(this.marker);
		}
	},
	getDistance: function(position) {
		try {
			this.distance = google.maps.geometry.spherical.computeDistanceBetween(position, this.marker.getPosition());
		} catch (e) {
//			console.log(e.name + ' : ' + e.message);
			this.distance = null;
		}
		return this.distance;
	},
	setDisplayByArea: function(arrayWordsSearch, searchOptions, bounds, map, listStoreFound, markerClusterer) {
		var isShow = true;
		var string = "";

		searchOptions.each((function(searchOption) {
			if (this.checkVar(this[searchOption])) {
				string += this[searchOption];
			}
		}).bind(this));

		string = string.toLocaleLowerCase();

		for (var j = 0, len = arrayWordsSearch.length; j < len; j++) {
			if (string.search(arrayWordsSearch[j]) === -1) {
				isShow = false;
				break;
			}
		}
		if (isShow) {
			bounds.extend(this.marker.getPosition());
			this.show();
			markerClusterer.addMarker(this.marker);
			listStoreFound.push(this);
		} else {
			this.hide();
			markerClusterer.removeMarker(this.marker);
		}
	},
	toHtml: function(storeTemplateId) {
		if (!this.checkVar(storeTemplateId)) {
			var storeTemplateId = this.storeOptions.storeTemplateId;
		}
		var storeDom = $(storeTemplateId).clone(true);
		storeDom.id = this.getStoreBoxId();
		storeDom.addClassName(this.storeOptions.storeClassItem);

		if (storeDom.down('.image_icon')) {
			storeDom.down('.image_icon').src = this.image_src;
		}

		var storeTemplate = new Template(storeDom.innerHTML);
		storeDom.innerHTML = storeTemplate.evaluate(this);

		Event.observe(storeDom, 'mouseover', (function() {
			this.marker.setAnimation(google.maps.Animation.BOUNCE);
		}).bindAsEventListener(this));

		Event.observe(storeDom, 'mouseout', (function() {
			this.marker.setAnimation(null);
		}).bindAsEventListener(this));

		return storeDom;
	},
	toInfoWindowHtml: function(templateId, elementId) {
		if ($(templateId))
			var domElement = $(templateId).clone(true);
		domElement.down('img.image_icon').src = this.image_src;
		domElement.down('img.image_icon').className = 'image_icon img-responsive';
		domElement.down('img.image_icon').setAttribute('style', 'width: 100%; height:100px;');

		var template = new Template(domElement.innerHTML);
		domElement.innerHTML = template.evaluate(this);
		domElement.id = elementId;

		domElement.select('.nav-tabs a').each(function(aTab) {
			aTab.observe('click', function() {
				domElement.select('.nav-tabs li').invoke('removeClassName', 'active');
				domElement.select('.tab-pane').invoke('removeClassName', 'active');
				aTab.up('li').addClassName('active');
				$(aTab.getAttribute('aria-controls')).addClassName('active');
			});
		});

		return domElement;
	}
};

var fadeEffect = function() {
	return {
		init: function(id, flag, target) {
			this.elem = document.getElementById(id);
			clearInterval(this.elem.si);
			this.target = target ? target : flag ? 100 : 0;
			this.flag = flag || -1;
			this.alpha = this.elem.style.opacity ? parseFloat(this.elem.style.opacity) * 100 : 0;
			this.elem.si = setInterval(function() {
				fadeEffect.tween()
			}, 20);
		},
		tween: function() {
			if (this.alpha == this.target) {
				clearInterval(this.elem.si);
			} else {
				var value = Math.round(this.alpha + ((this.target - this.alpha) * .05)) + (1 * this.flag);
				this.elem.style.opacity = value / 100;
				this.elem.style.filter = 'alpha(opacity=' + value + ')';
				this.alpha = value
			}
		}
	}
}();


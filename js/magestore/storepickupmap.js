var MapStorePickup = Class.create({
    map: null,
    circle: null,
    currentMarker: null,
    gmarkers: [],
    option: null,
    geocoder: new google.maps.Geocoder(),
    infoWindow: new google.maps.InfoWindow({ 
        size: new google.maps.Size(300,300)
    }),
    initialize: function (option) {
        this.option = option;
        this.map = new google.maps.Map(document.getElementById(option.mapId), {zoom:5,center:new google.maps.LatLng(0,0)});
        new google.maps.places.Autocomplete(document.getElementById(option.placeInputId));
        var countLength = option.stores.length;
        var bounds = new google.maps.LatLngBounds();
        for(var i = 0;i < countLength; i++){
            bounds.extend(this.createMarker(option.stores[i]).getPosition());
        }
        this.map.fitBounds(bounds);
        var StoreMap = this;
        $(this.option.searchAddress).observe('click',function(){
            StoreMap.codeAddress();
        });
        $(this.option.radiusInputId).observe('change', function(){
            StoreMap.changeRadius();
        });
    },
    createMarker: function (store) {
        var latlng = new google.maps.LatLng(store.store_latitude,store.store_longitude);
        var marker = new google.maps.Marker({
            position: latlng,
//            animation: google.maps.Animation.DROP,
            map: this.map,
            title: store.store_name,
            storeId: store.store_id,
            distance: 0,
            zoomLevel: store.zoom_level
        });
        var storeMap = this;
        google.maps.event.addListener(marker, 'click', function() {
            storeMap.map.setCenter(latlng);
            storeMap.map.setZoom(Number(marker.zoomLevel));
            storeMap.infoWindow.setContent(store.store_name+' '+store.address);
            storeMap.infoWindow.open(storeMap.map,marker);
            if(storeMap.currentMarker)
                storeMap.currentMarker.setAnimation(null);
            storeMap.currentMarker = marker;
            if (marker.getAnimation() != null) {
                marker.setAnimation(null);
            } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }        
        });
        this.gmarkers.push(marker);
        return marker;
    },
    codeAddress: function() {
        var searchCenter = 0;
        var address = document.getElementById(this.option.placeInputId).value;
        var radius = parseInt(document.getElementById(this.option.radiusInputId).value, 10)*1000;
        var StoreMap = this;
        this.geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                searchCenter = results[0].geometry.location;
                StoreMap.map.setCenter(searchCenter);
                StoreMap.map.setZoom(Math.round(14-Math.log(radius/1000)/Math.LN2));
                if (StoreMap.circle) StoreMap.circle.setMap(null);
                StoreMap.circle = new google.maps.Circle({
                    center:searchCenter,
                    radius: radius,
                    fillOpacity: 0.35,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#B9D3EE",
                    fillOpacity: 0.35,
                    map: StoreMap.map
                });
                StoreMap.gmarkers.each(function(el){
                    el.distance = google.maps.geometry.spherical.computeDistanceBetween(el.getPosition(),searchCenter);
                });
                StoreMap.gmarkers.sort(function(a, b){return a.distance - b.distance;});
                StoreMap.showByRadius(radius);
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    },
    showByRadius: function(radius){
        var foundMarkers = 0;
        var StoreMap = this;
        this.gmarkers.each(function(el){
            if(el.distance < radius){
                el.setMap(StoreMap.map);
                foundMarkers++;
            }else el.setMap(null);
        });
        return foundMarkers;
    },
    changeRadius: function(){
        if (this.circle){
            var center = this.circle.getCenter();
            var radius = Number($(this.option.radiusInputId).value)*1000;
            this.circle.setMap(null);
            this.circle = new google.maps.Circle({
                center: center,
                radius: radius,
                fillOpacity: 0.35,
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#B9D3EE",
                fillOpacity: 0.35,
                map: this.map
            });
            this.showByRadius(radius);
        }
    }
});

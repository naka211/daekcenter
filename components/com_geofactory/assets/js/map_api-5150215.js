function sleepMulti(a) {
    for (var b = (new Date).getTime(); b + a >= (new Date).getTime(););
    repos += 500
}

function DistanceWidget(a) {
    this.gfMap = a, this.set("map", a.map), this.set("position", a.centerPointRadius);
    var b = new google.maps.MarkerImage(a.dynRadIcoCenter, new google.maps.Size(20, 20), new google.maps.Point(0, 0), new google.maps.Point(10, 10)),
        c = new google.maps.Marker({
            draggable: !0,
            icon: b,
            title: a.dynRadTxtCenter
        });
    c.bindTo("map", this), c.bindTo("position", this);
    var d = new RadiusWidget(a);
    d.bindTo("map", this), d.bindTo("center", this, "position"), this.bindTo("distance", d), this.bindTo("bounds", d), this.getRadius = function() {
        return d.get("distance")
    }, this.setMap = function() {
        this.set("map", null)
    }, this.getBounds = function() {
        return d.get("bounds")
    }
}

function RadiusWidget(a) {
    this.gfMap = a;
    var b = new google.maps.Circle({
        strokeWeight: 2,
        strokeColor: a.sourceData.common.colorRadius,
        fillColor: a.sourceData.common.colorRadius,
        fillOpacity: .15
    });
    this.set("distance", a.radValue), this.bindTo("bounds", b), b.bindTo("center", this), b.bindTo("map", this), b.bindTo("radius", this), this.addSizer_()
}

function displayInfo(a) {
    updatePosition(a.gfMap.nameAddressInput), distance = a.get("distance"), distance > a.gfMap.dynRadDistMax && (distance = a.gfMap.dynRadDistMax);
    var b = document.getElementById(a.gfMap.nameRadiusSelect),
        c = document.createElement("INPUT");
    c.id = a.gfMap.nameRadiusSelect, c.type = "text", c.style.width = "50px", c.className = "gfMapControls", c.value = Math.round(100 * distance) / 100, b = b.parentNode.replaceChild(c, b)
}

function updatePosition(a) {
    distWidgetTimer && window.clearTimeout(distWidgetTimer), distWidgetTimer = window.setTimeout(function() {
        reverseGeocodePosition(a), distanceWidget.gfMap.searchLocationsFromPoint(distanceWidget.get("position"))
    }, 500)
}

function reverseGeocodePosition(a) {
    var b = distanceWidget.get("position"),
        c = new google.maps.Geocoder,
        d = document.getElementById(a),
        e = Math.round(100 * b.lat()) / 100 + "," + Math.round(100 * b.lng()) / 100;
    d.blur(), setTimeout(function() {
        d.value = ""
    }, 10), c.geocode({
        latLng: b
    }, function(a, b) {
        return b == google.maps.GeocoderStatus.OK && a[1] ? (d.placeholder = a[1].formatted_address, d.title = e, void 0) : void 0
    }), d.placeholder = e, d.title = e
}

function js_array_interset(a, b) {
    for (var c = new Array; a.length > 0 && b.length > 0;) a[0] < b[0] ? a.shift() : a[0] > b[0] ? b.shift() : (c.push(a.shift()), b.shift());
    return c
}

function inArrayMj(a, b) {
    for (var c = b.length, d = 0; c > d; d++)
        if (b[d] == a) return !0;
    return !1
}

function isCustomSize(a, b) {
    return 1 > a ? !1 : 1 > b ? !1 : !0
}

function clsTile(a, b, c, d, e) {
    this.name = a, this.size = b, this.png = c, this.zoom = d, this.alt = e, this.fct = null, this.createTile = function(a) {
        var b = new google.maps.ImageMapType({
            getTileUrl: this.fct,
            tileSize: new google.maps.Size(this.size, this.size),
            isPng: this.png,
            maxZoom: this.zoom,
            name: this.name,
            alt: this.alt
        });
        a.mapTypes.set(this.name, b)
    }, this.getBingUrl = function(a, b, c) {
        function g(a, b) {
            for (; a.length < b;) a = "0" + a;
            return a
        }
        for (var d = g(String(b.x.toString(2)), c + 1), e = g(String(b.y.toString(2)), c + 1), f = "", h = 0; h < e.length; h += 1) f += e[h] + d[h];
        return f = parseInt("0" + f, 2), a + g(f.toString(4), c) + ".jpeg?g=915&mkt=en-us&n=z"
    }
}

function clsGfMarker(a, b) {
    this.id = a.id, this.name = a.rt, this.lat = a.lat, this.lng = a.lng, this.curUser = a.cu, this.type = a.tl, this.level = 0, 1 == b && (this.level = a.lv), this.liste = a.idL, this.distance = a.di, this.icon = a.mi, this.shadow = a.om, this.resizeh = parseInt(a.rh), this.resizew = parseInt(a.rw), this.zoomMe = a.zm, this.trace = a.tr, this.salesRad = a.sa, this.zindex = a.zi, this.factor = 1, this.point = new google.maps.LatLng(parseFloat(this.lat), parseFloat(this.lng)), this.iconMarker = null, this.iconOmbre = null, this.ggMarker = null, this.ggCircle = null, this.draw = function(a) {
        this.ggMarker = new google.maps.Marker({
            position: this.point,
            map: a,
            animation: google.maps.Animation.DROP
        }), this.ggMarker.setIcon(this.iconMarker), this.ggMarker.setShadow(this.iconOmbre), this.ggMarker.setTitle(this.name), this.ggMarker.setZIndex(5e4 - parseInt(this.zindex))
    }, this.drawTrace = function(a) {
        this.trace && this.trace.length > 3 && (this.ggTrace = new google.maps.KmlLayer(this.trace, {
            map: a,
            preserveViewport: !1,
            suppressInfoWindows: !0
        }))
    }, this.drawSalesArea = function(a, b, c, d) {
        if (lastSalesCircle && c && lastSalesCircle.setMap(null), !(this.salesRad && this.salesRad <= 0)) {
            var e = this.salesRad * b;
            this.ggCircle = new google.maps.Circle({
                map: a,
                center: this.point,
                radius: e,
                strokeOpacity: 1,
                strokeWeight: 1,
                strokeColor: d,
                fillColor: d,
                fillOpacity: .15
            }), lastSalesCircle = this.ggCircle
        }
    }, this.drawUniqueTrace = function(a, b) {
        kmlGpsTool && kmlGpsTool.setMap(null), this.trace && this.trace.length > 3 && (kmlGpsTool = new google.maps.KmlLayer(this.trace, {
            map: a,
            preserveViewport: b,
            suppressInfoWindows: !0
        }))
    }, this.prepareIcon = function(a) {
        this.icon.length < 1 || (1 == this.shadow && (this.iconOmbre = new google.maps.MarkerImage(a, new google.maps.Size(20, 20), new google.maps.Point(0, 0), new google.maps.Point(10, 10))), this.iconMarker = this.icon.length > 3 && !isCustomSize(this.resizeh, this.resizew) ? new google.maps.MarkerImage(this.icon) : new google.maps.MarkerImage(this.icon, new google.maps.Size(this.resizew, this.resizeh), new google.maps.Point(0, 0), new google.maps.Point(this.resizew / 2, 0), new google.maps.Size(this.resizew, this.resizeh)))
    }
}

function clsGfPline(a) {
    this.idA = a.id1, this.idB = a.id1, this.pointA = new google.maps.LatLng(parseFloat(a.x1), parseFloat(a.y1)), this.pointB = new google.maps.LatLng(parseFloat(a.x2), parseFloat(a.y2)), this.color = a.col, this.draw = function(a, b) {
        if (!(a.length < 1)) {
            var c = [this.pointA, this.pointB];
            return new google.maps.Polyline({
                path: c,
                geodesic: !0,
                strokeColor: this.color,
                strokeOpacity: 1,
                strokeWeight: b,
                zIndex: 100,
                clickable: !1
            })
        }
    }
}

function clsGfSpecial(a, b) {
    this.type = a.tl, this.liste = a.idL, this.icon = a.mi, this.resizeh = parseInt(a.rh), this.resizew = parseInt(a.rw), this.accom = a.pt, this.max = a.mx, this.open = a.op, this.mode = a.md, this.icon.length > 0 && ("undefined" != typeof b[this.liste] && this.icon.length > 3 && (this.icon = b[this.liste] + this.icon), this.icon = this.icon.length > 3 && !isCustomSize(this.resizeh, this.resizew) ? new google.maps.MarkerImage(this.icon) : new google.maps.MarkerImage(this.icon, new google.maps.Size(this.resizew, this.resizeh), new google.maps.Point(0, 0), new google.maps.Point(this.resizew / 2, 0), new google.maps.Size(this.resizew, this.resizeh)))
}

function clsGfList(a, b) {
    this.id = a.id, this.name = a.name, this.type = a.type, this.useSide = a.useSide, this.bubblewidth = a.bubblewidth, this.plines = a.plines, this.commonIconPath = a.commonIconPath, this.markersid = a.markers.split(","), this.level = 0, 1 == b && (this.level = a.level)
}

function clsGfMap() {
    this.map = null, this.imageWait = null, this.imageGalWait = null, this.imageGalClose = null, this.imageGalPrev = null, this.imageGalNext = null, this.imageShadow = null, this.iconeUser1 = null, this.iconeUser2 = null, this.iconeUser3 = null, this.iconMyPos = null, this.dynRadIcoCenter = null, this.dynRadIcoDrag = null, this.dirService = null, this.dirDisplay = null, this.geocoder = new google.maps.Geocoder, this.mapOption = {}, this.centerPointDefault = new google.maps.LatLng(0, 0), this.centerPointDefaultPhp = new google.maps.LatLng(0, 0), this.centerPointRadius = new google.maps.LatLng(0, 0), this.radCircle = new google.maps.Circle, this.boundaries = new google.maps.LatLngBounds, this.autoComplete = null, this.useMemberPos = !1, this.useBrowserPos = !1, this.useBoundary = !1, this.useNoDoublon = !1, this.useNoTmplDoublon = !1, this.useRoutePlaner = !1, this.exclusiveView = !1, this.articleView = !1, this.useCluster = !1, this.useSelector = !1, this.useTabs = !1, this.useSideTemplate = 0, this.drawRadiusCircle = !1, this.markerCluster = null, this.radUnit = "km", this.radFact = 1e3, this.earthRadius = 6371, this.dynRadDistMax = 50, this.radValue = 0, this.curProfileId = 0, this.markersList = new Array, this.plinesList = new Array, this.xmlFile = null, this.xmlFileDyn = null, this.xmlSuffixe = null, this.xmlSuffixeCatDyn = "", this.sourceData = new Array, this.curSelectorVal = null, this.clickMarker = "click", this.clickMarkerTrack = "click", this.clickSales = 0, this.trackZoom = !0, this.forceZoom = 0, this.listIdForSide = null, this.strictCountTemplate = null, this.userCleanLoad = !1, this.cleanLoad = !1, this.vCommonPath = [], this.vBubbleWidth = [], this.maxMarkers = 0, this.niveaupresent = 0, this.nameMapContainer = "TYPE_gf_ID", this.nameWaitArea = "gf_waitZone", this.nameSidelistPremium = "gf_sidelistPremium", this.nameSidelists = "gf_sidelists", this.nameSidebar = "gf_sidebar", this.nameRadiusSelect = "radiusSelect", this.nameAddressInput = "addressInput", this.nameSelectorMS = "gf_list_selector", this.nameMultiSelectorMS = "gf_multi_selector", this.nameStreetView = "gf_streetView", this.nameTransport = "gf_transport", this.nameItineraire = "gf_routepanel", this.nameDebugCont = "", this.nameMjrsRefLat = "mj_rs_ref_lat", this.nameMjrsRefLng = "mj_rs_ref_lng", this.nanameMjrsRefRad = "mj_rs_radius_selector", this.idsNoStreetView = "No streetview available here.", this.dynRadTxtCenter = "Move the center", this.dynRadTxtDrag = "Move the radius size", this.getMapUrl = function(a) {
        return gf_sr + commonUrl + "&task=map.getJson&" + a
    }, this.checkMapData = function(a) {
        return parseInt(a.id) < 1 ? !1 : !0
    }, this.setMapInfo = function(a, b) {
        switch (this.nameMapContainer = b, this.setDefaultImages(a.common_image_path), this.idsNoStreetView = a.idsNoStreetView, this.dynRadTxtCenter = a.dynRadTxtCenter, this.dynRadTxtDrag = a.dynRadTxtDrag, 255 != a.ss_artLat && (a.centerlat = a.ss_artLat, a.centerlng = a.ss_artLng, this.articleView = !0), this.setCenterPointDefault(parseFloat(a.centerlat), parseFloat(a.centerlng), parseFloat(a.centerlatPhpGc), parseFloat(a.centerlngPhpGc)), this.setMMZ(parseInt(a.minZoom), parseInt(a.maxZoom)), this.setMTB(a.mapTypeOnStart, parseInt(a.mapControl), a.mapTypeBar), this.setDWZ(parseInt(a.doubleClickZoom), parseInt(a.wheelZoom)), this.setMZC(parseInt(a.mapsZoom)), this.setSMC(a.mapTypeControl), this.setCTRL(parseInt(a.pegman), parseInt(a.scaleControl), parseInt(a.rotateControl), parseInt(a.overviewMapControl)), "undefined" != typeof a.niveaux && (this.niveaupresent = parseInt(a.niveaux)), this.createMap(), this.setStyle(a.mapStyle), this.autoCompleteStart(a.acCountry, parseInt(a.acTypes)), this.addNote(parseInt(a.hideNote)), this.maxMarkers = parseInt(a.totalmarkers), this.setMouse(parseInt(a.bubbleOnOver), parseInt(a.salesRadMode), parseInt(a.trackOnOver), parseInt(a.trackZoom)), this.initCluster(parseInt(a.useCluster), parseInt(a.endZoom), parseInt(a.gridSize), a.imagePath, a.imageSizes, parseInt(a.minClustSize)), this.setUseRoutePlaner(parseInt(a.useRoutePlaner)), a.cleanLoad > 0 && (this.userCleanLoad = !0, this.setCleanLoad()), (parseInt(a.radFormMode) > 1 || 1 == parseInt(a.templateAuto)) && jQuery("#gf_map_panel").length > 0 && (this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById("gf_map_panel")), jQuery("#gf_map_panel").slideDown("slow")), parseInt(a.forceZoom) > 0 && (this.forceZoom = a.forceZoom), 1 == parseInt(a.ss_zoomMeProf) && a.ss_zoomMeId > 0 && this.setProfileView(a.ss_zoomMeId), this.dynRadDistMax = parseInt(a.dynRadDistMax), 1 == parseInt(a.drawCircle) && (this.drawRadiusCircle = !0), 1 == parseInt(a.fe_rad_unit) && this.setRadiusUnit("mi", 1609, 3959), 2 == parseInt(a.fe_rad_unit) && this.setRadiusUnit("nm", 1852, 3440), 1 == parseInt(a.allowDbl) && (this.useNoDoublon = !0), 3 == parseInt(a.allowDbl) && (this.useNoTmplDoublon = !0), parseInt(a.centerUser)) {
            case 1:
                this.useMemberPos = !0;
                break;
            case 2:
                this.useBrowserPos = !0, this.centerMapOnBrowser();
                break;
            case 3:
                this.useBoundary = !0
        }
        this.manageMapClick(parseInt(a.clickRadius)), 1 == parseInt(a.useTabs) && (this.useTabs = !0)
    }, this.setDefaultImages = function(a) {
        this.imageGalWait = a + "icon_wait.gif", this.imageShadow = a + "marker_avatar.gif", this.dynRadIcoCenter = a + "dynRadCenter.png", this.dynRadIcoDrag = a + "dynRadSizer.png", this.imageWait = '<img src="' + this.imageGalWait + '" />', this.setIconMyPos(a + "locate_me.gif")
    }, this.setCenterPointDefault = function(a, b, c, d) {
        this.centerPointDefaultPhp = new google.maps.LatLng(c, d), this.centerPointDefault = new google.maps.LatLng(a, b), 0 == c && 0 == d && (this.centerPointDefaultPhp = this.centerPointDefault), centerPointGFmap = this.centerPointDefault
    }, this.setMouse = function(a, b, c, d) {
        1 == a && (this.clickMarker = "mouseover"), 2 == a && (this.clickMarker = "rightclick"), 3 == a && (this.clickMarker = 3), 1 == b && (this.clickSales = "click"), 2 == b && (this.clickSales = "rightclick"), 3 == b && (this.clickSales = "mouseover"), 1 == c && (this.clickMarkerTrack = "mouseover"), 2 == c && (this.clickMarkerTrack = "rightclick"), 3 == c && (this.clickMarkerTrack = 3), 1 == d && (this.trackZoom = !1)
    }, this.setCleanLoad = function() {
        this.userCleanLoad && (this.cleanLoad = !0)
    }, this.selectRadiusVal = function(a) {
        a > 0 && jQuery("#" + this.nameRadiusSelect).val(a)
    }, this.setRadiusUnit = function(a, b, c) {
        this.radUnit = a, this.radFact = b, this.earthRadius = c
    }, this.setProfileView = function(a) {
        this.exclusiveView = !0, this.curProfileId = a
    }, this.setCTRL = function(a, b, c, d) {
        this.mapOption.streetViewControl = 1 == a ? !0 : !1, this.mapOption.scaleControl = 1 == b ? !0 : !1, this.mapOption.rotateControl = 1 == c ? !0 : !1, this.mapOption.overviewMapControl = 1 == d ? !0 : !1
    }, this.setMMZ = function(a, b) {
        b = parseInt(b), a = parseInt(a), 1 > b || 1 > a || (a >= b ? (this.mapOption.minZoom = b, this.mapOption.maxZoom = a) : (this.mapOption.minZoom = a, this.mapOption.maxZoom = b))
    }, this.setMTB = function(a, b) {
        switch (a) {
            case "HYBRID":
                this.mapOption.mapTypeId = google.maps.MapTypeId.HYBRID;
                break;
            case "ROADMAP":
                this.mapOption.mapTypeId = google.maps.MapTypeId.ROADMAP;
                break;
            case "SATELLITE":
                this.mapOption.mapTypeId = google.maps.MapTypeId.SATELLITE;
                break;
            default:
            case "TERRAIN":
                this.mapOption.mapTypeId = google.maps.MapTypeId.TERRAIN
        }
        return 0 == b ? (this.mapOption.mapTypeControl = !1, void 0) : (this.mapOption.mapTypeControl = !0, this.mapOption.mapTypeControlOptions = {
            style: google.maps.MapTypeControlStyle.mTypeBar
        }, void 0)
    }, this.setDWZ = function(a, b) {
        this.mapOption.disableDoubleClickZoom = 1 == a ? !1 : !0, this.mapOption.scrollwheel = 1 == b ? !0 : !1
    }, this.setSMC = function(a) {
        return 0 == a ? (this.mapOption.zoomControl = !1, this.mapOption.panControl = !1, void 0) : (this.mapOption.zoomControl = !0, this.mapOption.panControl = !0, this.mapOption.zoomControlOptions = {
            style: google.maps.ZoomControlStyle.mCtrl
        }, void 0)
    }, this.setMZC = function(a) {
        a = parseInt(a), (a > 24 || 1 > a) && (a = 14), this.mapOption.center = this.centerPointDefault, this.mapOption.zoom = a
    }, this.setStyle = function(a) {
        a && a.length > 0 && (a = JSON.parse(a), this.map.setOptions({
            styles: a
        }))
    }, this.createMap = function() {
        this.map = new google.maps.Map(document.getElementById(this.nameMapContainer), this.mapOption), google.maps.event.addListenerOnce(this.map, "idle", function() {
            if (jQuery("#gf_mm_parent")) try {
                moveMap(1)
            } catch (a) {}
        })
    }, this.loadDynCat = function(a, b, c, d) {
        axLoadDyncat(a, b, c, d)
    }, this.autoCompleteStart = function(a, b) {
        if (!(jQuery("#" + this.nameAddressInput).length < 1)) {
            var c = "";
            1 == b && (c = "establishment"), 2 == b && (c = "geocode"), 3 == b && (c = "(cities)");
            var d = {};
            c.length > 1 && (d.types = [c]), a.length > 1 && (d.componentRestrictions = {
                country: a
            });
            var e = document.getElementById(this.nameAddressInput);
            this.autocomplete = new google.maps.places.Autocomplete(e, d), this.autocomplete.bindTo("bounds", this.map);
            var f = this;
            google.maps.event.addListener(this.autocomplete, "place_changed", function() {
                f.SLFI()
            }), me = this, "function" == typeof onMapLoaded && onMapLoaded(me)
        }
    }, this.addNote = function(a) {
        1 > a || (copyrightNode = document.createElement("div"), copyrightNode.id = "gf_note-control", copyrightNode.style.fontSize = "10px", copyrightNode.style.fontFamily = "Arial, sans-serif", copyrightNode.style.margin = "0 2px 2px 0", copyrightNode.style.whiteSpace = "nowrap", copyrightNode.index = 0 /*,copyrightNode.innerHTML='<a href="http://www.myjoom.com" target="_blank">Map & Markers</a>'*/ , this.map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(copyrightNode))
    }, this.setUseRoutePlaner = function(a) {
        1 > a || (this.useRoutePlaner = !0, this.dirService = new google.maps.DirectionsService, this.dirDisplay = new google.maps.DirectionsRenderer, this.dirDisplay.setPanel(document.getElementById(this.nameItineraire)), this.dirDisplay.setMap(this.map))
    }, this.addKmlLayer = function(a) {
        new google.maps.KmlLayer(a, {
            map: this.map,
            preserveViewport: !0
        })
    }, this.centerMapOnBrowser = function() {
        this.exclusiveView || this.articleView || this.getBrowserPos(!1, !1)
    }, this.setIconMyPos = function(a) {
        this.iconMyPos = new google.maps.MarkerImage(a, new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 16))
    }, this.getBrowserPos = function(a, b) {
        map = this.map;
        var c = this;
        if (iconMyPos = this.iconMyPos, defautPos = this.centerPointDefaultPhp, 3 == arguments.length && (defautPos = arguments[2]), navigator.geolocation) navigator.geolocation.getCurrentPosition(function(d) {
            ptBrowserPos = new google.maps.LatLng(d.coords.latitude, d.coords.longitude), b && c.searchLocationsFromPoint(ptBrowserPos), onsuccessBrowserPos(map, ptBrowserPos, a, iconMyPos)
        }, function() {
            b && c.searchLocationsFromPoint(defautPos), onsuccessBrowserPos(map, defautPos, a, iconMyPos)
        });
        else if (google.gears) {
            var d = google.gears.factory.create("beta.geolocation");
            d.getCurrentPosition(function(d) {
                ptBrowserPos = new google.maps.LatLng(d.latitude, d.longitude), b && c.searchLocationsFromPoint(ptBrowserPos), onsuccessBrowserPos(map, ptBrowserPos, a, iconMyPos, b)
            }, function() {
                b && c.searchLocationsFromPoint(defautPos), onsuccessBrowserPos(map, defautPos, a, iconMyPos)
            })
        } else map.panTo(defautPos), alert("Geocode not supported by this browser.")
    }, this.manageMapClick = function(a) {
        var b = this;
        return 1 == a ? (google.maps.event.addListener(this.map, "rightclick", function() {
            iw && iw.close(), iw = new google.maps.InfoWindow
        }), google.maps.event.addListener(this.map, "click", function(a) {
            iw && iw.close(), iw = new google.maps.InfoWindow, b.searchLocationsFromPoint(a.latLng)
        }), void 0) : 2 == a ? (google.maps.event.addListener(this.map, "rightclick", function(a) {
            iw && iw.close(), iw = new google.maps.InfoWindow, b.searchLocationsFromPoint(a.latLng)
        }), google.maps.event.addListener(this.map, "click", function() {
            iw && iw.close(), iw = new google.maps.InfoWindow
        }), void 0) : (google.maps.event.addListener(this.map, "rightclick", function() {
            iw && iw.close(), iw = new google.maps.InfoWindow
        }), google.maps.event.addListener(this.map, "click", function() {
            iw && iw.close(), iw = new google.maps.InfoWindow
        }), void 0)
    }, this.searchLocationsFromInput = function() {
        if (address = jQuery("#" + this.nameAddressInput).val(), address.length < 1) return this.searchLocationsFromPoint(null), void 0;
        var a = this;
        this.geocoder.geocode({
            address: address,
			region : 'da'
        }, function(b, c) {
            c == google.maps.GeocoderStatus.OK ? a.searchLocationsFromPoint(b[0].geometry.location) : alert("Geocode was not successful for the following reason: " + c)
        })
    }, this.initCircle = function() {
        this.radCircle && this.radCircle.setMap(null)
    }, this.initRadiusValue = function() {
        this.radValue = -1, jQuery("#" + this.nameRadiusSelect).length > 0 && (this.radValue = jQuery("#" + this.nameRadiusSelect).val())
    }, this.drawRadCircle = function() {
        if (this.centerPointRadius && !(!this.drawRadiusCircle || this.radValue <= 0 || 0 == this.centerPointRadius.lat() && 0 == this.centerPointRadius.lng())) {
            var a = this;
            this.radCircle = new DistanceWidget(a), distanceWidget = this.radCircle, google.maps.event.addListener(this.radCircle, "distance_changed", function() {
                displayInfo(distanceWidget)
            }), google.maps.event.addListener(this.radCircle, "position_changed", function() {
                displayInfo(distanceWidget)
            })
        }
    }, this.showWaiter = function() {
        jQuery("#gf_NbrMarkersPre").length > 0 && (this.nameWaitArea = "gf_NbrMarkersPre"), jQuery("#" + this.nameWaitArea).html(this.imageWait)
    }, this.hideWaiter = function() {
        jQuery("#" + this.nameWaitArea).html(""), updateCounter()
    }, this.cleanSide = function(a) {
        jQuery("#" + a).length > 0 && jQuery("#" + a).html("")
    }, this.setXmlFile = function(a, b, c, d) {
        xmlFile = gf_sr + commonUrl + "&task=map.getJson&task=markers.getJson&idmap=" + c + "&Itemid=" + d + a, this.xmlFileDyn = xmlFile, this.xmlSuffixe = "", 1 == b && (xmlFile = gf_sr + "cache/_geoFactory_" + c + "_" + d + ".json"), this.centerPointRadius && 0 != this.centerPointRadius.lat() && 0 != this.centerPointRadius.lng() && jQuery("#" + this.nameRadiusSelect).length > 0 && (this.radValue = jQuery("#" + this.nameRadiusSelect).val(), this.xmlSuffixe = "", xmlFile = this.xmlFileDyn), this.xmlFile = xmlFile, this.isDebugMode() && (jQuery("#" + this.nameDebugCont).html("xml file"), jQuery("#" + this.nameDebugCont).attr("href", this.xmlFile + this.xmlSuffixe + this.xmlSuffixeCatDyn))
    }, this.initCluster = function(a, b, c, d, e, f) {
        if (1 > a) return this.useCluster = !1, void 0;
        this.useCluster = !0;
        var g = {
            maxZoom: b,
            gridSize: c,
            minimumClusterSize: f
        };
        d.length > 0 && (g.imagePath = d, e.length > 0 && (g.imageSizes = e.split(","))), this.markerCluster = new MarkerClusterer(this.map, [], g), this.markerCluster.onClick = function(a) {
            return multiChoice(a.cluster_, this.map)
        }
    }, this.setClusters = function() {
        if (this.useCluster) {
            if (this.markerCluster && (this.markerCluster.clearMarkers(), this.markerCluster.setMap(null), this.markerCluster.setMap(this.map)), ggMarkers = [], this.markersList.length > 0)
                for (var a in this.markersList) null != this.markersList[a].ggMarker && this.markersList[a].ggMarker.getVisible() && (this.markersList[a].ggMarker.tmpImageWait = this.imageWait, this.markersList[a].ggMarker.tmpMarkerDist = this.markersList[a].distance, this.markersList[a].ggMarker.tmpBubleWidth = this.markersList[a].width, this.markersList[a].ggMarker.tmpMarkerId = this.markersList[a].id, this.markersList[a].ggMarker.tmpListId = this.markersList[a].liste, ggMarkers.push(this.markersList[a].ggMarker));
            this.markerCluster.addMarkers(ggMarkers)
        }
    }, this.initDrawable = function(a) {
        if (!(a.length < 1)) {
            for (var b = 0; b < a.length; b++) a[b].ggMarker ? (a[b].ggMarker.setMap(null), a[b].ggCircle && a[b].ggCircle.setMap(null)) : a[b].setMap(null);
            a.length = 0
        }
    }, this.getMarkersFromJson = function(a) {
        this.sourceData.listes = this.createLists(a.lists), this.sourceData.markers = this.createMarkers(a.markers), this.sourceData.plines = this.createPlines(a.plines), this.sourceData.common = a.common, this.sourceData.spec = this.createSpecial(a.spec)
    }, this.createLists = function(a) {
        var b = new Array;
        if (a)
            for (var c = 0; c < a.length; c++) {
                var d = new clsGfList(a[c], this.niveaupresent);
                b.push(d), d.commonIconPath.length > 2 && (this.vCommonPath[d.id] = d.commonIconPath), this.vBubbleWidth[d.id] = d.bubblewidth, d.useSide > 0 && d.useSide > this.useSideTemplate && (this.useSideTemplate = d.useSide)
            }
        return b
    }, this.createMarkers = function(a) {
        var b = new Array;
        if (a)
            for (var c = 0; c < a.length; c++) {
                var d = new clsGfMarker(a[c], this.niveaupresent);
                if ("undefined" != typeof this.vCommonPath[d.liste] && d.icon.length > 3 && (d.icon = this.vCommonPath[d.liste] + d.icon), d.width = this.vBubbleWidth[d.liste], this.useNoDoublon && c > 0) {
                    var e = !0;
                    if (b.length > 0)
                        for (var f in b)
                            if (b[f].type == d.type && b[f].id == d.id) {
                                e = !1;
                                break
                            }
                    if (!e) continue
                }
                b.push(d)
            }
        return b
    }, this.createSpecial = function(a) {
        var b = new Array;
        if (a)
            for (var c = 0; c < a.length; c++)
                if (a[c].pt) {
                    var d = new clsGfSpecial(a[c], this.vCommonPath);
                    b.push(d)
                } else console.log("GooglePlace markerset error: no types defined!");
        return b
    }, this.createPlines = function(a) {
        var b = new Array;
        if (a)
            for (var c = 0; c < a.length; c++) {
                var d = new clsGfPline(a[c]);
                b.push(d)
            }
        return b
    }, this.filterAllowedSelector = function(a) {
        if (!this.useSelector) return !0;
        var b = a.type + "_" + a.liste;
        for (i = 0; i < this.curSelectorVal.length; i++) {
            if (0 == this.curSelectorVal[i]) return !0;
            if (this.curSelectorVal[i] == b) return !0
        }
        return !1
    }, this.drawMarkers = function() {
        if (!(this.sourceData.markers.length < 1)) {
            var a = this,
                b = !1;
            this.listIdForSide = new Array;
            for (var c = 0; c < this.sourceData.markers.length; c++) {
                var d = this.sourceData.markers[c];
                (this.useMemberPos && 1 == d.curUser || this.exclusiveView && 1 == d.zoomMe) && (this.map.panTo(d.point), b = !0, (this.radValue <= 0 || null == this.centerPointRadius || 0 == this.centerPointRadius.lat() && 0 == this.centerPointRadius.lng()) && (this.radValue = 5e4), this.centerPointRadius = d.point), d.prepareIcon(this.imageShadow), d.draw(this.map), 0 == this.clickSales && d.drawSalesArea(this.map, this.radFact, !1, this.sourceData.common.colorSales), manageMarkerClick(a, d), this.markersList.push(d)
            }
            this.useMemberPos && !b && this.map.panTo(this.centerPointDefault)
        }
    }, this.setMarkerInSide = function(a) {
        jQuery("#" + this.nameSidelists).length > 0 && this.createSidebarEntry(a, "gf_sidelists"), jQuery("#" + this.nameSidebar).length > 0 && this.createSidebarEntry(a, "gf_sidebar"), jQuery("#" + this.nameSidelistPremium).length > 0 && this.createSidebarEntry(a, "gf_sidelistPremium")
    }, this.createSidebarEntry = function(a, b) {
        var c = "gf_list_" + a.type + "_id_" + a.liste,
            d = "gf_list_" + a.type + "_id_" + a.liste + "_idMarker_" + a.id,
            e = document.createElement("div"),
            f = a.name;
        if (a.distance = Math.round(100 * a.distance) / 100, "gf_sidelistPremium" == b && (c = "gf_list_Premium_" + a.liste, d = "gf_list_Premium_idMarker_" + a.id), a.distance > 0 && (f = f + " (" + a.distance + this.radUnit + " )"), e.innerHTML = f, e.title = f, e.id = d, 3 != this.clickMarker && (e.style.cursor = "pointer"), e.style.marginTop = "5px", e.style.width = "95%", e.onclick = function() {
                google.maps.event.trigger(a.ggMarker, "click"), google.maps.event.trigger(a.ggMarker, "rightclick")
            }, this.useSideTemplate > 0 && (e.innerHTML = "", e.title = ""), document.getElementById(c)) document.getElementById(c).appendChild(e);
        else {
            var g = this.getListName(a),
                h = document.createElement("div");
            f = '<div class="gf-sidelist-heading">' + g + "</div>", h.id = c, h.innerHTML = f, this.useSideTemplate > 0 && 1 == this.useNoTmplDoublon && (h.innerHTML = "", h.title = ""), "gf_sidelists" == b ? h.setAttribute("style", "padding:2px;float:left; width:150px;max-height:150px;overflow:scroll; word-wrap:break-word;border:1px solid grey;") : "gf_sidebar" == b ? h.setAttribute("style", "padding:2px;float:left; width:150px;word-wrap:break-word;border:1px solid grey;") : "gf_sidelistPremium" == b && h.setAttribute("style", "padding:2px;word-wrap:break-word;"), h.appendChild(e), document.getElementById(b).appendChild(h)
        }
        if (1 == this.useSideTemplate && axGetSideItem(a.id, a.liste, a.distance, d, this.isDebugMode()), 2 == this.useSideTemplate) {
            if (1 == this.useNoTmplDoublon) {
                var i = a.type + "_id_" + a.id;
                if (inArrayMj(i, this.strictCountTemplate)) return;
                this.strictCountTemplate.push(i)
            }
            this.listIdForSide[a.liste + "#" + d] || (this.listIdForSide[a.liste + "#" + d] = new Array), a.ggMarker.getVisible() && (this.listIdForSide[a.liste + "#" + d].push(a.id), this.listIdForSide[a.liste + "#" + d].push(a.distance))
        }
    }, this.loadSidelistFromTemplate = function() {
        if (!(this.useSideTemplate < 2))
            for (var a in this.listIdForSide)
                if (this.listIdForSide.hasOwnProperty(a)) {
                    var b = a.split("#");
                    axGetSideItemOnce(b[0], this.listIdForSide[a], b[1], this.isDebugMode())
                }
    }, this.getListName = function(a) {
        for (var b = 0; b < this.sourceData.listes.length; b++)
            if (this.sourceData.listes[b].type == a.type && this.sourceData.listes[b].id == a.liste) return this.sourceData.listes[b].name;
        return "Listing"
    }, this.isCurrentProfileMarker = function(a) {
        return !this.exclusiveView || "MS_CB" != a.type && "MS_JS" != a.type || a.id != this.curProfileId ? !1 : !0
    }, this.checkZoomEntry = function(a) {
        return a.zoomMe < 1 ? !1 : (this.exclusiveView || (this.boundaries = new google.maps.LatLngBounds, this.exclusiveView = !0), !0)
    }, this.addBounds = function(a) {
        if (this.checkZoomEntry(a)) return this.boundaries.extend(a.point), void 0;
        if (this.useBoundary || this.exclusiveView) return this.isCurrentProfileMarker(a) ? (this.boundaries.extend(a.point), void 0) : (this.useBoundary && !this.exclusiveView && this.boundaries.extend(a.point), void 0)
    }, this.getValueOfSelectors = function() {
        this.curSelectorVal = new Array, jQuery("#" + this.nameMultiSelectorMS).length > 0 && (this.curSelectorVal = jQuery("#" + this.nameMultiSelectorMS).val() || []);
        var a = this.curSelectorVal;
        jQuery("#gf_toggeler input:checked").each(function() {
            a.push(this.name)
        }), this.curSelectorVal = a, jQuery("#" + this.nameSelectorMS).length > 0 && (jQuery("#" + this.nameSelectorMS).val().length > 1 || "0" == jQuery("#" + this.nameSelectorMS).val()) && this.curSelectorVal.push(jQuery("#" + this.nameSelectorMS).val()), this.curSelectorVal.length > 0 && (this.useSelector = !0)
    }, this.displayXmlContent = function(a) {
        this.listIdForSide = new Array, a && (this.drawMarkers(), this.drawPlines()), this.checkMarkers(), this.checkPlines(), this.cleanLoad = !1, this.setClusters(), this.loadSidelistFromTemplate(), this.hideWaiter(), this.drawRadCircle(), this.setBoundary(), this.addPlaces()
    }, this.addPlaces = function() {
        if (!(this.sourceData.spec.length < 1)) {
            for (var a in gpMarkers)
                if (gpMarkers.hasOwnProperty(a))
                    for (var b = 0; b < gpMarkers[a].length; b++) gpMarkers[a].hasOwnProperty(b) && gpMarkers[a][b].setMap(null);
            gpMarkers = new Array;
            for (var c = 0; c < this.sourceData.spec.length; c++) {
                var d = {};
                if (d.liste = this.sourceData.spec[c].liste, gpMarkers[d.liste] = new Array, this.isListActive(d.liste, "MS_GP")) {
                    var e = this.sourceData.spec[c];
                    d.map = this.map, d.types = e.accom, d.icon = e.icon, d.max = e.max, d.mode = e.mode, d.openNow = e.open, d.imageWait = this.imageWait, this.radValue <= 0 || null == this.centerPointRadius || 0 == this.centerPointRadius.lat() && 0 == this.centerPointRadius.lng() ? d.bounds = this.map.getBounds() : (d.radius = this.radValue * this.radFact, d.location = this.centerPointRadius), searchPlaces(d)
                }
            }
        }
    }, this.listeChecked = function(a) {
        if (!this.useSelector || !this.curSelectorVal) return !0;
        if (this.curSelectorVal.length < 1) return !1;
        var b = a.type + "_" + a.id;
        for (i = 0; i < this.curSelectorVal.length; i++) {
            if (0 == this.curSelectorVal[i]) return !0;
            if (this.curSelectorVal[i] == b) return !0
        }
        return !1
    }, this.enableMarkersFromChk = function() {
        for (var a = new Array, c = 0; c < this.sourceData.listes.length; c++) {
            var d = this.sourceData.listes[c].markersid,
                e = this.sourceData.listes[c].type;
            if (e in a || (a[e] = new Array), this.listeChecked(this.sourceData.listes[c])) {
                if (!this.useSelector || !this.curSelectorVal) {
                    for (var f = 0; f < d.length; f++) a[e].indexOf(d[f]) < 0 && a[e].push(d[f]);
                    continue
                }
                if (0 == this.sourceData.listes[c].level)
                    for (var f = 0; f < d.length; f++) a[e].indexOf(d[f]) < 0 && a[e].push(d[f])
            } else if (this.sourceData.listes[c].level > 0) {
                var g = a[e];
                g = g.filter(function(a) {
                    return d.indexOf(a) < 0
                }), a[e] = g
            }
        }
        return a
    }, this.checkMarkers = function() {
        numberofMarkers = 0;
        var a = new Array;
        this.strictCountTemplate = new Array;
        var b = this.enableMarkersFromChk();
        if (this.radValue > 0 && null != this.centerPointRadius && 0 != this.centerPointRadius.lat() && 0 != this.centerPointRadius.lng() && this.markersList.length > 0) {
            for (var c in this.markersList) null != this.markersList[c].ggMarker && "undefined" != this.markersList[c].ggMarker && (this.markersList[c].distance = this.getDistanceBetweenPoints(this.centerPointRadius, this.markersList[c].ggMarker.getPosition()));
            this.markersList.sort(function(a, b) {
                return a.zoomMe != b.zoomMe ? a.zoomMe ? -1 : 1 : a.distance - b.distance
            })
        }
        if (this.markersList.sort(function(a, b) {
                return a.level < b.level ? -1 : a.level > b.level ? 1 : 0
            }), new Array, this.markersList.length > 0) {
            for (var c in this.markersList)
                if (null != this.markersList[c].ggMarker && "undefined" != this.markersList[c].ggMarker) {
                    var f = !1;
                    if (this.markersList[c].type in b) {
                        var g = {
                            type: this.markersList[c].type,
                            id: this.markersList[c].liste
                        };
                        f = b[this.markersList[c].type].indexOf(this.markersList[c].id) >= 0 && this.listeChecked(g) ? !0 : !1
                    }
                    if (this.maxMarkers > 0 && a.length >= this.maxMarkers ? f = !1 : f && 0 == this.cleanLoad && (f = this.markerInRadius(this.markersList[c])), f) {
                        var h = this.markersList[c].type + "-" + this.markersList[c].id;
                        (this.useNoDoublon || this.useNoTmplDoublon) && inArrayMj(h, a) && (f = !1)
                    }
                    this.markersList[c].ggMarker.setVisible(f), this.markersList[c].ggCircle && this.markersList[c].ggCircle.setVisible(f), f && (a.push(h), this.setMarkerInSide(this.markersList[c]), this.addBounds(this.markersList[c]))
                }(this.useNoDoublon || this.useNoTmplDoublon) && (a = a.getUnique()), numberofMarkers = a.length
        }
    }, this.checkPlines = function() {
        if (this.plinesList.length > 0)
            for (i = 0; i < this.plinesList.length; i++)
                if (null != this.plinesList[i] && "undefined" != this.plinesList[i]) {
                    var a = !1;
                    0 == this.cleanLoad && (this.markerInRadius(this.plinesList[i].getPath().getAt(0)) && this.markerInRadius(this.plinesList[i].getPath().getAt(1)) && (a = !0), 1 == a && this.useSelector && this.curSelectorVal && (a = this.isPlineActive(this.plinesList[i]))), this.plinesList[i].setVisible(a)
                }
    }, this.isPlineActive = function() {
        return this.curSelectorVal.length < 1 ? !1 : !1
    }, this.isMarkerActive = function(a) {
        if (this.curSelectorVal.length < 1) return !1;
        var b = a.type + "_" + a.liste;
        for (i = 0; i < this.curSelectorVal.length; i++) {
            if (0 == this.curSelectorVal[i]) return !0;
            if (this.curSelectorVal[i] == b) return !0
        }
        return !1
    }, this.isListActive = function(a, b) {
        if (!this.useSelector) return !0;
        if (this.curSelectorVal.length < 1) return !1;
        var c = b + "_" + a;
        for (i = 0; i < this.curSelectorVal.length; i++) {
            if (0 == this.curSelectorVal[i]) return !0;
            if (this.curSelectorVal[i] == c) return !0
        }
        return !1
    }, this.markerInRadius = function(a) {
        if (this.radValue <= 0 || null == this.centerPointRadius || 0 == this.centerPointRadius.lat() && 0 == this.centerPointRadius.lng()) return !0;
        if (a.ggMarker && "undefined" != a.ggMarker) {
            var b = a.distance,
                c = parseFloat(this.radValue) + parseFloat(a.salesRad);
            if (-1 == b || b > c) return !1
        } else {
            var b = this.getDistanceBetweenPoints(this.centerPointRadius, a);
            if (-1 == b || b > parseFloat(this.radValue)) return !1
        }
        return !0
    }, this.getDistanceBetweenPoints = function(a, b) {
        if (!a || !b) return -1;
        var c = this.earthRadius,
            d = (b.lat() - a.lat()) * Math.PI / 180,
            e = (b.lng() - a.lng()) * Math.PI / 180,
            f = Math.sin(d / 2) * Math.sin(d / 2) + Math.cos(a.lat() * Math.PI / 180) * Math.cos(b.lat() * Math.PI / 180) * Math.sin(e / 2) * Math.sin(e / 2),
            g = 2 * Math.atan2(Math.sqrt(f), Math.sqrt(1 - f)),
            h = c * g;
        return h
    }, this.isDebugMode = function() {
        return this.nameDebugCont && jQuery("#" + this.nameDebugCont).length > 0 ? !0 : !1
    }, this.manageSourceResult = function(a, b) {
        if (this.getMarkersFromJson(a), this.displayXmlContent(!0), this.isDebugMode()) {
            var c = (new Date).getMilliseconds(),
                d = c - b;
            console.log("Source file parsed in " + d + " milliseconds")
        }
        jQuery("#" + this.nameMapContainer).fadeTo("slow", 1).css("background", "none")
    }, this.initFromMjRadiusSearchApp = function() {
        jQuery("#" + this.nameMjrsRefLat).length < 1 || jQuery("#" + this.nameMjrsRefLng).length < 1 || this.centerPointRadius || (0 != jQuery("#" + this.nameMjrsRefLat).val() || 0 != jQuery("#" + this.nameMjrsRefLng).val()) && jQuery("#" + this.nanameMjrsRefRad).length > 0 && (this.radValue = jQuery("#" + this.nanameMjrsRefRad).val(), this.centerPointRadius = new google.maps.LatLng(jQuery("#" + this.nameMjrsRefLat).val(), jQuery("#" + this.nameMjrsRefLng).val()))
    }, this.searchLocationsFromDyn = function(a, b) {
        var c = jQuery("#" + a.id).val();
        this.xmlSuffixeCatDyn = "&fc=" + c + "&ext=" + b, this.cleanSide(this.nameSidelistPremium), this.cleanSide(this.nameSidelists), this.cleanSide(this.nameSidebar), this.initDrawable(this.markersList), this.initDrawable(this.plinesList), this.sourceData = new Array, this.markersList = new Array, this.searchLocationsFromPoint(1), dynCatLastExt = b, dynCatLastId = c
    }, this.searchLocationsFromPoint = function(a) {
        1 == a ? (a = null, this.xmlSuffixe = "") : 2 == a ? a = this.centerPointRadius : (this.xmlSuffixeCatDyn = "", dynCatLastExt && dynCatLastExt.length > 1 && (this.xmlSuffixeCatDyn = "&fc=" + dynCatLastId + "&ext=" + dynCatLastExt)), this.boundaries = new google.maps.LatLngBounds, this.centerPointRadius = a, this.showWaiter(), this.cleanSide(this.nameSidelistPremium), this.cleanSide(this.nameSidelists), this.cleanSide(this.nameSidebar), this.initRadiusValue(), this.initCircle(), this.getValueOfSelectors(), this.initFromMjRadiusSearchApp();
        var b = this,
            c = this.xmlFile + this.xmlSuffixe + this.xmlSuffixeCatDyn,
            d = (new Date).getMilliseconds();
        "undefined" == this.sourceData.markers || !this.sourceData.markers || this.sourceData.markers.length < 1 || this.xmlSuffixeCatDyn.length > 0 ? jQuery.getJSON(c, function(a) {
            b.manageSourceResult(a, d)
        }) : (this.cleanLoad = !1, this.displayXmlContent(!1)), this.setResizeTrig(), me = this, "function" == typeof onRadiusPosChanged && onRadiusPosChanged(me)
    }, this.setResizeTrig = function() {
        google.maps.event.trigger(this.map, "resize")
    }, this.setBoundary = function() {
        if (this.radCircle.getRadius() > 0) return this.map.fitBounds(this.radCircle.getBounds()), centerPointGFmap = this.centerPointRadius, void 0;
        if (this.forceZoom > 0 && this.map.setZoom(this.forceZoom), !this.articleView && (this.useBoundary || this.exclusiveView)) {
            if (this.boundaries.isEmpty()) return this.map.panTo(this.centerPointDefault), centerPointGFmap = this.centerPointDefault, void 0;
            this.map.fitBounds(this.boundaries), centerPointGFmap = this.boundaries.getCenter(), this.forceZoom > 0 && this.map.setZoom(this.forceZoom)
        }
    }, this.addPlineListener = function(a) {
        if (this.useRoutePlaner) {
            a.setOptions({
                clickable: !0
            });
            var b = this;
            google.maps.event.addListener(a, "click", function() {
                pts = a.getPath();
                var d = jQuery("#" + b.nameTransport).val(),
                    e = {
                        origin: pts.getAt(0),
                        destination: pts.getAt(1),
                        travelMode: google.maps.TravelMode[d]
                    };
                b.dirService.route(e, function(a, c) {
                    c == google.maps.DirectionsStatus.OK && b.dirDisplay.setDirections(a)
                })
            });
            var c = {
                    strokeWeight: 2 * b.strokeWeight,
                    strokeOpacity: 1
                },
                d = {
                    strokeWeight: b.strokeWeight,
                    strokeOpacity: .9
                };
            google.maps.event.addListener(a, "mouseover", function() {
                a.setOptions(c)
            }), google.maps.event.addListener(a, "mouseout", function() {
                a.setOptions(d)
            })
        }
    }, this.drawPlines = function() {
        if (!(this.sourceData.plines.length < 1))
            for (var a = 0; a < this.sourceData.plines.length; a++) {
                var b = this.sourceData.plines[a].draw(this.markersList, this.strokeWeight);
                b && (b.setMap(this.map), this.addPlineListener(b), this.plinesList.push(b))
            }
    }, this.SLFDYN = function(a, b) {
        this.searchLocationsFromDyn(a, b)
    }, this.SLFI = function() {
        this.searchLocationsFromInput()
    }, this.SLFP = function() {
        this.searchLocationsFromPoint(2)
    }, this.SLRES = function() {
        this.setCleanLoad(), this.searchLocationsFromPoint(null)
    }, this.SLFPZR = function(a, b) {
        this.searchLocationsFromPoint(a, b)
    }, this.LMBTN = function() {
        this.getBrowserPos(!0, !1)
    }, this.NMBTN = function() {
        this.getBrowserPos(!1, !0)
    }
}

function updateCounter() {
    jQuery("#gf_NbrMarkersPre").length > 0 && jQuery("#gf_NbrMarkersPre").html(numberofMarkers)
}

function searchPlaces(a) {
    gpPlaces = new google.maps.places.PlacesService(a.map), 0 == a.mode ? gpPlaces.nearbySearch(a, function(b, c) {
        drawPlaces(b, c, a)
    }) : 1 == a.mode && gpPlaces.radarSearch(a, function(b, c) {
        drawPlaces(b, c, a)
    })
}

function drawPlaces(a, b, c) {
    if (b == google.maps.places.PlacesServiceStatus.OK) {
        numberofMarkers = a.length > c.max && c.max > 0 ? parseInt(numberofMarkers) + parseInt(c.max) : parseInt(numberofMarkers) + parseInt(a.length), updateCounter();
        for (var d = 0; d < a.length; d++) {
            if (c.max > 0 && gpMarkers[c.liste].length == c.max) return;
            gpMarkers[c.liste].push(new google.maps.Marker({
                position: a[d].geometry.location,
                icon: c.icon,
                title: a[d].name
            })), google.maps.event.addListener(gpMarkers[c.liste][d], "click", getDetails(a[d], gpMarkers[c.liste][d], c.map, c.imageWait, c.liste)), window.setTimeout(dropMarker(gpMarkers[c.liste][d], c), 10 * d)
        }
    }
}

function dropMarker(a, b) {
    return function() {
        a && a.setMap(b.map)
    }
}

function getDetails(a, b, c, d, e) {
    return function() {
        gpPlaces.getDetails({
            reference: a.reference
        }, showInfoWindow(b, c, d, e))
    }
}

function showInfoWindow(a, b, c, d) {
    return function(e, f) {
        if (iw && iw.close(), iw = new google.maps.InfoWindow, iw.setContent(c), f == google.maps.places.PlacesServiceStatus.OK) {
            var g;
            try {
                "undefined" != typeof ActiveXObject ? g = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (g = new XMLHttpRequest)
            } catch (h) {}
            "function" == typeof onBubbleOpen && onBubbleOpen(b, a);
            var i = "",
                j = "",
                k = "";
            if (e.formatted_phone_number && (i = e.formatted_phone_number), k = e.website ? e.website : e.url, e.rating)
                for (var l = 0; 5 > l; l++) j += e.rating < l + .5 ? "&#10025;" : "&#10029;";
            var m = {
                    name: e.name,
                    icon: e.icon,
                    url: e.url,
                    website: k,
                    address: e.vicinity,
                    phone: i,
                    rating: j
                },
                n = gf_sr + commonUrl + "&task=marker.bubblePl" + "&idL=" + d + "&pt=" + a.getPosition().lat() + "," + a.getPosition().lng() + "&data=" + JSON.stringify(m);
            g.open("GET", n, !0), document.getElementById("gf_debugmode_bubble") && (document.getElementById("gf_debugmode_bubble").innerHTML = "bubble link", document.getElementById("gf_debugmode_bubble").href = n), g.onreadystatechange = function() {
                if (4 == g.readyState) {
                    var c = g.responseText;
                    iw.setContent(c), iw.open(b, a)
                }
            }, g.send(null)
        }
    }
}

function manageMarkerClick(a, b) {
    3 != a.clickMarker && google.maps.event.addListener(b.ggMarker, a.clickMarker, function() {
        b.distance = a.getDistanceBetweenPoints(a.centerPointRadius, b.ggMarker.getPosition()), -1 != b.distance && (b.distance = Math.round(100 * b.distance) / 100, b.distance = b.distance + a.radUnit), iw.setContent(a.imageWait), iw.setOptions({
            maxWidth: b.width
        }), iw.open(a.map, b.ggMarker), panoramaOptions = {
            position: b.ggMarker.getPosition(),
            navigationControl: !0,
            enableCloseButton: !0,
            addressControl: !0,
            linksControl: !1
        }, manageBubbleLoad(a, b.point), axGetBubble(iw, b.id, b.liste, b.distance, b.ggMarker, a.map, a.isDebugMode())
    }), 0 != a.clickSales && google.maps.event.addListener(b.ggMarker, a.clickSales, function() {
        b.drawSalesArea(a.map, a.radFact, !0, a.sourceData.common.colorSales)
    }), 3 != a.clickMarkerTrack && (google.maps.event.addListener(b.ggMarker, a.clickMarkerTrack, function() {
        b.drawUniqueTrace(a.map, a.trackZoom)
    }), kmlGpsTool && google.maps.event.addListener(kmlGpsTool, a.clickMarker, function() {
        google.maps.event.trigger(b.ggMarker, a.clickMarker)
    }))
}

function manageBubbleLoad(a, b) {
    google.maps.event.addListener(iw, "domready", function() {
        a.useTabs && $(function() {
            $("#gftabs").tabs()
        }), jQuery("#gfdirws").click(function() {
            var c = jQuery("#" + a.nameTransport).val(),
                d = jQuery("#saddr").val(),
                e = {
                    origin: d,
                    destination: b,
                    travelMode: google.maps.TravelMode[c]
                };
            a.dirService.route(e, function(b, c) {
                c == google.maps.DirectionsStatus.OK && a.dirDisplay.setDirections(b)
            })
        }), jQuery("#gflmws").click(function() {
            var a = new google.maps.Geocoder;
            navigator.geolocation ? navigator.geolocation.getCurrentPosition(function(b) {
                a.geocode({
                    latLng: new google.maps.LatLng(b.coords.latitude, b.coords.longitude)
                }, function(a, b) {
                    b == google.maps.GeocoderStatus.OK && jQuery("#saddr").val(a[0].formatted_address)
                })
            }) : alert("Your browser is not compatible")
        }), jQuery("#gflm").click(function() {
            if (navigator.geolocation) navigator.geolocation.getCurrentPosition(function(b) {
                ptBrowserPos = new google.maps.LatLng(b.coords.latitude, b.coords.longitude), onsuccessBrowserPos(a.map, ptBrowserPos, !0, a.iconMyPos), iw.close()
            });
            else if (google.gears) {
                var b = google.gears.factory.create("beta.geolocation");
                b.getCurrentPosition(function(b) {
                    ptBrowserPos = new google.maps.LatLng(b.latitude, b.longitude), onsuccessBrowserPos(a.map, ptBrowserPos, !0, a.iconMyPos), iw.close()
                })
            } else alert("Geocode not possible in this browser.")
        }), jQuery("#SV").click(function() {
            var b = new google.maps.StreetViewService;
            b.getPanoramaByLocation(panoramaOptions.position, 49, function(b, c) {
                if (c == google.maps.StreetViewStatus.OK) {
                    var d = new google.maps.StreetViewPanorama(document.getElementById(a.nameStreetView), panoramaOptions);
                    a.map.setStreetView(d)
                } else jQuery("#" + a.nameStreetView).css("color", "red"), jQuery("#" + a.nameStreetView).css("textAlign", "center"), jQuery("#" + a.nameStreetView).html(a.idsNoStreetView)
            })
        })
    })
}

function onsuccessBrowserPos(a, b, c, d) {
    a.panTo(b), c && (locateMarker && locateMarker.setMap(null), locateMarker = new google.maps.Marker({
        position: b,
        icon: d,
        map: a,
        draggable: !0
    }), a.setZoom(17))
}

function axGetBubble(a, b, c, d, e, f, g) {
    var h;
    try {
        "undefined" != typeof ActiveXObject ? h = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (h = new XMLHttpRequest)
    } catch (i) {}
    "function" == typeof onBubbleOpen && onBubbleOpen(f, e);
    var j = gf_sr + commonUrl + "&task=marker.bubble&idU=" + b + "&idL=" + c + "&dist=" + d + "&pt=" + e.getPosition().lat() + "," + e.getPosition().lng();
    g && console.log(j), h.open("GET", j, !0), document.getElementById("gf_debugmode_bubble") && (document.getElementById("gf_debugmode_bubble").innerHTML = "bubble link", document.getElementById("gf_debugmode_bubble").href = j), h.onreadystatechange = function() {
        if (4 == h.readyState) {
            var b = h.responseText;
            a.setContent(b), a.open(f, e)
        }
    }, h.send(null)
}

function axLoadDyncat(a, b, c, d) {
    var e;
    try {
        "undefined" != typeof ActiveXObject ? e = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (e = new XMLHttpRequest)
    } catch (f) {}
    var g = gf_sr + commonUrl + "&task=markers.dyncat&ext=" + a + "&idP=" + b + "&mapVar=" + d;
    e.open("GET", g, !0), e.onreadystatechange = function() {
        if (4 == e.readyState) {
            var a = e.responseText;
            a.length > 0 && (jQuery("#" + c).html(e.responseText), jQuery("#" + c).fadeIn("slow"))
        }
    }, e.send(null)
}

function axGetSideItem(a, b, c, d, e) {
    var f;
    try {
        "undefined" != typeof ActiveXObject ? f = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (f = new XMLHttpRequest)
    } catch (g) {}
    jQuery("#" + d).fadeOut("slow");
    var h = gf_sr + commonUrl + "&task=marker.side&idU=" + a + "&idL=" + b + "&dist=" + c;
    e && console.log(h), f.open("GET", h, !0), document.getElementById("gf_debugmode_side_item") && (document.getElementById("gf_debugmode_side_item").innerHTML = "Sidelist link", document.getElementById("gf_debugmode_bubble").href = h), f.onreadystatechange = function() {
        if (4 == f.readyState) {
            var a = f.responseText;
            a.length > 0 && (jQuery("#" + d).html(f.responseText), jQuery("#" + d).fadeIn("slow"))
        }
    }, f.send(null)
}

function axGetSideItemOnce(a, b, c, d) {
    var e;
    try {
        "undefined" != typeof ActiveXObject ? e = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (e = new XMLHttpRequest)
    } catch (f) {}
    jQuery("#" + c).fadeOut("slow");
    var g = gf_sr + commonUrl + "&task=marker.fullSide&idsDists=" + JSON.stringify(b) + "&idL=" + a;
    d && console.log(g), e.open("GET", g, !0), document.getElementById("gf_debugmode_side_item") && (document.getElementById("gf_debugmode_side_item").innerHTML = "Sidelist link", document.getElementById("gf_debugmode_bubble").href = g), e.onreadystatechange = function() {
        if (4 == e.readyState) {
            var a = e.responseText;
            a.length > 0 && (jQuery("#" + c).html(e.responseText), jQuery("#" + c).fadeIn("slow"))
        }
    }, e.send(null)
}

function axSavePos(a, b, c, d) {
    var f, e = d || !1;
    try {
        "undefined" != typeof ActiveXObject ? f = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (f = new XMLHttpRequest)
    } catch (g) {}
    document.getElementById(c.nameWaitArea).innerHTML = c.imageWait, f.open("GET", gf_sr + "index.php?option=com_geocode_factory&task=axSaveMyPosition&lat=" + a + "&lng=" + b + "&fetchaddress=" + e, !0), f.onreadystatechange = function() {
        if (4 == f.readyState) {
            var a = f.responseText;
            document.getElementById(c.nameWaitArea).innerHTML = a, jQuery("#" + c.nameWaitArea).html(), c.SLFP()
        }
    }, f.send(null)
}

function checkBox(a) {
    var b = document.createElement("DIV");
    b.className = "checkboxContainer", b.title = a.title;
    var c = document.createElement("SPAN");
    c.role = "checkbox", c.className = "checkboxSpan";
    var d = document.createElement("DIV");
    d.className = "blankDiv", d.id = a.id, bDivs.push(a.id);
    var e = document.createElement("IMG");
    e.className = "blankImg", e.src = "http://maps.gstatic.com/mapfiles/mv/imgs8.png", "https:" === location.protocol && (e.src = "https://maps.gstatic.com/mapfiles/mv/imgs8.png");
    var f = document.createElement("LABEL");
    return f.className = "checkboxLabel", f.innerHTML = a.label, d.appendChild(e), c.appendChild(d), b.appendChild(c), b.appendChild(f), google.maps.event.addDomListener(b, "click", function() {
        document.getElementById(d.id).style.display = "block" == document.getElementById(d.id).style.display ? "none" : "block", "block" == document.getElementById(d.id).style.display ? layers[a.id].setMap(a.gmap) : layers[a.id].setMap(null)
    }), b
}

function optionDivCust(a, b) {
    var c;
    if (b && "undefined" != b && b.length > 3) var c = document.getElementById(b);
    else {
        var c = document.createElement("DIV");
        c.className = "dropDownItemDiv", c.title = a.title, c.id = a.id, c.innerHTML = a.name, google.maps.event.addDomListener(c, "click", a.action)
    }
    return c
}

function optionDiv(a) {
    var b = document.createElement("DIV");
    return b.className = "dropDownItemDiv", b.title = a.title, b.id = a.id, b.innerHTML = a.name, google.maps.event.addDomListener(b, "click", function() {
        for (var a in layers) layers[a].setMap(null);
        for (var a in bDivs) bDivs[a] && bDivs[a].length > 1 && document.getElementById(bDivs[a]) && document.getElementById(bDivs[a]).style && (document.getElementById(bDivs[a]).style.display = "none")
    }), b
}

function separator() {
    var a = document.createElement("DIV");
    return a.className = "separatorDiv", a
}

function dropDownOptionsDiv(a) {
    var b = document.createElement("DIV");
    b.className = "dropDownOptionsDiv", b.id = a.id;
    for (var c = 0; c < a.items.length; c++) b.appendChild(a.items[c]);
    return b
}

function dropDownControl(a) {
    var b = document.createElement("DIV");
    b.className = "gf_container";
    var c = document.createElement("DIV");
    c.className = "dropDownControl", c.innerHTML = a.name, c.id = a.name;
    var d = document.createElement("IMG");
    d.src = "http://maps.gstatic.com/mapfiles/arrow-down.png", "https:" === location.protocol && (d.src = "https://maps.gstatic.com/mapfiles/arrow-down.png"), d.className = "dropDownArrow", c.appendChild(d), b.appendChild(c), b.appendChild(a.dropDown), a.gmap.controls[a.position].push(b), google.maps.event.addDomListener(b, "click", function() {
        switchPanel("myddOptsDiv", 6e3)
    })
}

function switchPanel(a, b) {
    "block" == jQuery("#" + a).css("display") ? jQuery("#" + a).fadeOut("slow") : jQuery("#" + a).fadeIn("slow"), b > 0 && setTimeout(function() {
        jQuery("#" + a).fadeOut("slow")
    }, b)
}

function multiChoice(a, b) {
    if (markers = a.getMarkers(), markers.length > 1) {
        ids = "";
        for (var c = 0; c < markers.length; c++) 0 == c ? ids += markers[c].tmpMarkerId : ids = ids + "," + markers[c].tmpMarkpourrerId, ids = markers[c].tmpMarkerDist > 0 ? ids + "," + markers[c].tmpMarkerDist : ids + "," + "-1";
        return iw.setContent(markers[0].tmpImageWait), iw.setOptions({
            maxWidth: markers[0].tmpBubleWidth
        }), iw.open(b, markers[0]), axGetMultiBubble(b, markers[0], markers[0].tmpListId, ids), !1
    }
    return !0
}

function axGetMultiBubble(a, b, c, d) {
    var e;
    try {
        "undefined" != typeof ActiveXObject ? e = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (e = new XMLHttpRequest)
    } catch (f) {}
    var g = gf_sr + commonUrl + "&task=marker.bubbleMulti&idsDists=" + JSON.stringify(d) + "&idL=" + c;
    e.open("GET", g, !0), e.onreadystatechange = function() {
        if (4 == e.readyState) {
            var c = e.responseText;
            iw.setContent(c), iw.open(a, b)
        }
    }, e.send(null)
}
var commonUrl = "index.php?option=com_geofactory",
    numberofMarkers = 0,
    gpMarkers = new Array,
    gpPlaces, panoramaOptions, locateMarker, iw = new google.maps.InfoWindow,
    marker_user_1, marker_user_2, marker_user_3, centerPointGFmap, kmlGpsTool, lastSalesCircle, dynCatLastExt, dynCatLastId, distanceWidget, distWidgetTimer, bDivs = [],
    layers = {
        traffic: new google.maps.TrafficLayer,
        transit: new google.maps.TransitLayer,
        biking: new google.maps.BicyclingLayer
    };
google.maps.weather && (layers.cloud = new google.maps.weather.CloudLayer, layers.weatherF = new google.maps.weather.WeatherLayer({
    temperatureUnits: google.maps.weather.TemperatureUnit.FAHRENHEIT
}), layers.weatherC = new google.maps.weather.WeatherLayer({
    temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS
}));
var repos = 0;
DistanceWidget.prototype = new google.maps.MVCObject, RadiusWidget.prototype = new google.maps.MVCObject, RadiusWidget.prototype.distance_changed = function() {
    distance = this.get("distance"), distance > this.gfMap.dynRadDistMax && (distance = this.gfMap.dynRadDistMax), this.set("radius", distance * this.gfMap.radFact)
}, RadiusWidget.prototype.addSizer_ = function() {
    var a = new google.maps.MarkerImage(this.gfMap.dynRadIcoDrag, new google.maps.Size(26, 10), new google.maps.Point(0, 0), new google.maps.Point(13, 5)),
        b = new google.maps.Marker({
            draggable: !0,
            icon: a,
            title: this.gfMap.dynRadTxtDrag
        });
    b.bindTo("map", this), b.bindTo("position", this, "sizer_position");
    var c = this;
    google.maps.event.addListener(b, "drag", function() {
        c.setDistance()
    })
}, RadiusWidget.prototype.center_changed = function() {
    var a = this.get("bounds");
    if (a) {
        var b = a.getNorthEast().lng(),
            c = new google.maps.LatLng(this.get("center").lat(), b);
        this.set("sizer_position", c)
    }
}, RadiusWidget.prototype.setDistance = function() {
    var a = this.get("sizer_position"),
        b = this.get("center"),
        c = this.gfMap.getDistanceBetweenPoints(b, a);
    this.set("distance", c)
};
var indexOf = function(a) {
    return indexOf = "function" == typeof Array.prototype.indexOf ? Array.prototype.indexOf : function(a) {
        var b = -1,
            c = -1;
        for (b = 0; b < this.length; b++)
            if (this[b] === a) {
                c = b;
                break
            }
        return c
    }, indexOf.call(this, a)
};
Array.prototype.getUnique = function() {
    for (var a = {}, b = [], c = 0, d = this.length; d > c; ++c) a.hasOwnProperty(this[c]) || (b.push(this[c]), a[this[c]] = 1);
    return b
};
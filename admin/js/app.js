(function() {
	var app = angular.module('monumentsManager', []);

	app.factory('monuments', function() {
        var monuments = {};

        return {
            getMonuments: function() {
            	return monuments;
            },
            setMonuments: function(value) {
            	monuments = value;
            }
        };
    });

	app.directive('monumentsList', function() {
		return {
			restrict: 'E',
			templateUrl: 'monuments-list.html',
			controller: ['$http', 'monuments', function($http, monuments) {
				monuments.setMonuments({});

				$http.get('http://37.187.216.159/shazam/api.php').success(function(data) {
					monuments.setMonuments(data['Search']);

					for(monument of monuments.getMonuments()) {
						monument.isActive = false;
						monument.languageId = 0;
					}
				});

				this.getMonuments = function() {
					return monuments.getMonuments();
				}

				this.editMonument = function(monument) {
					if(monument.editMode) {
						$http({
						    method: 'PUT',
						    url: 'http://37.187.216.159/shazam/api.php',
						    data: "id=" + monument.id + "&monument=" + JSON.stringify(monument),
						    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
						});
						monument.editMode = false;
					}
					else {
						monument.editMode = true;
					}
				}

				this.deleteMonument = function(monument) {
					$http({
					    method: 'DELETE',
					    url: 'http://37.187.216.159/shazam/api.php',
					    data: "id=" + monument.id,
					    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					});
					monuments.getMonuments().splice(monuments.getMonuments().indexOf(monument), 1);
				};

				this.toogleDetails = function(monument) {
					monument.isActive = !monument.isActive;
				};

				this.changeLanguage = function(monument, id) {
					monument.languageId = id;
				};
			}],
			controllerAs: 'monumentsCtrl'
		};
	});

	app.directive('monumentForm', function() {
		return {
			restrict: 'E',
			templateUrl: 'monument-form.html',
			controller: ['$http', 'monuments', function($http, monuments) {
				this.initMonument = function() {
					this.monument = {};
					this.monument.characteristics = [];
					this.monument.characteristics.push({'name': '', 'description': '', 'language': {'name': 'Français', 'value': 'fr-FR'}});
					this.monument.photoPath = '';
					this.monument.year = '';
					this.monument.localization = {'latitude': '', 'longitude': ''};
					this.monument.address = {'number': '', 'street': '', 'city': {'name': '', 'country': {'name': ''}}};
					this.monument.isActive = false;
					this.monument.languageId = 0;
					this.monument.editMode = false;
				}

				this.initMonument();

				this.isDisplayed = false;

				this.display = function() {
					this.isDisplayed = true;
				};

				this.hide = function() {
					this.isDisplayed = false;
				};

				this.submit = function() {
					$http({
					    method: 'POST',
					    url: 'http://37.187.216.159/shazam/api.php',
					    data: "monument=" + JSON.stringify(this.monument),
					    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					}).
					success(function(data, status) {
			          this.monument.id = data.id;
			        });
					
					monuments.getMonuments().push(this.monument);
					this.initMonument();
					this.hide();
				}
			}],
			controllerAs: 'form'
		};
	});
})();
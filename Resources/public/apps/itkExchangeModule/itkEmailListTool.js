angular.module('itkExchangeModule').directive('itkEmailListTool', [
  function () {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
      },
      link: function (scope, element, attrs) {
        scope.email = "";

        scope.addEmail = function () {
          if (!scope.slide.options.hasOwnProperty('resources')) {
            scope.slide.options.resources = [];
          }

          if (scope.email === '') {
            return;
          }

          scope.slide.options.resources.push({mail: scope.email});
        };

        scope.removeEmail = function (email) {
          scope.slide.options.resources.splice(
            scope.slide.options.resources.indexOf(email),
            1
          );
        };

      },
      templateUrl: '/bundles/itkexchange/apps/itkExchangeModule/itkEmailListTool.html'
    };
  }
]);

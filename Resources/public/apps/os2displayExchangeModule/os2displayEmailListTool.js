/**
 * @file
 * Contains a slide tool to add emails to slide.options.resources array.
 */

/**
 * Tool: os2display-email-list-tool.
 */
angular.module('os2displayExchangeModule').directive('os2displayEmailListTool', [
  function () {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
      },
      link: function (scope) {
        scope.email = "";

        /**
         * Add new resource with scope.email to resources.
         */
        scope.addEmail = function () {
          // Make sure slide.option.resources field is created.
          if (!scope.slide.options.hasOwnProperty('resources')) {
            scope.slide.options.resources = [];
          }

          // Only add email if it is not empty.
          if (scope.email === '') {
            return;
          }

          // Add to resources.
          scope.slide.options.resources.push({mail: scope.email});
        };

        /**
         * Remove an email from index in resources.
         *
         * @param index
         */
        scope.removeEmail = function (index) {
          scope.slide.options.resources.splice(index, 1);
        };
      },
      templateUrl: '/bundles/os2displayexchange/apps/os2displayExchangeModule/os2displayEmailListTool.html'
    };
  }
]);

angular.module('starter')
 
.controller('ImageController', function($scope, $cordovaDevice, $cordovaFile, $ionicPlatform, $cordovaEmailComposer, $ionicActionSheet, ImageService, FileService) {
 
  $ionicPlatform.ready(function() {
    $scope.images = FileService.images();
    $scope.$apply();
  });
 
  $scope.addMedia = function() {
    $scope.hideSheet = $ionicActionSheet.show({
      buttons: [
        { text: 'Take photo' },
        { text: 'Photo from library' }, 
          {  text: '<b ng-disabled="user.length<1">Delete</b>',
              type: 'input type="file"'}
      ],
      titleText: 'Add images',
      cancelText: 'Cancel',
      buttonClicked: function(index) {
        $scope.addImage(index);
      }
    });
  }
 
  $scope.addImage = function(type) {
    $scope.hideSheet();
    ImageService.handleMediaDialog(type).then(function() {
      $scope.$apply();
    });
  }
  
  $scope.sendEmail = function() {
    if ($scope.images != null && $scope.images.length > 0) {
      var mailImages = [];
      var savedImages = $scope.images;
      for (var i = 0; i < savedImages.length; i++) {
          mailImages.push('base64:attachment'+i+'.jpg//' + savedImages[i]);
        }
      $scope.openMailComposer(mailImages);
    }
  }
 
  $scope.openMailComposer = function(attachments) {
    var bodyText = '<html><h2>My Images</h2></html>';
    var email = {
        to: '',
        attachments: attachments,
        subject: 'Devdactic Images',
        body: bodyText,
        isHtml: true
      };
 
    $cordovaEmailComposer.open(email, function(){
        console.log('email view dismissed');                                                      
    }, this);
  }
});
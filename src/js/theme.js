$(function () {
    $('#add-new').click(function () {
        console.log('add new');
        window.location.replace("/profile.php?add");
    });

    $('#add-profile-cancel-button').click(function () {
       window.location.replace('/profile.php');
    });
});
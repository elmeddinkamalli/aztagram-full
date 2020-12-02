<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script> -->
<script src="./assets/js/jquery-3.4.1.min.js"></script>
<script src="./assets/js/main.js"></script>
<script>
    $(function(){
        if($('#user').length){
            var scriptForUser = document.createElement('script');
            scriptForUser.setAttribute('src','./assets/js/user.js');
            scriptForUser.setAttribute('id','user-script');
            document.body.appendChild(scriptForUser);
        }

        if($('#posts').length || $('#user').length){
            var loadMore = document.createElement('script');
            loadMore.setAttribute('src','./assets/js/loadMoreAjax.js');
            document.body.appendChild(loadMore);

            var postActions = document.createElement('script');
            postActions.setAttribute('src','./assets/js/post-actions.js');
            document.body.appendChild(postActions);
        }

        if($('#posts').length){
            var loadMore = document.createElement('script');
            loadMore.setAttribute('src','./assets/js/loadMoreComments.js');
            document.body.appendChild(loadMore);
        }

        if($('#edit-page').length){
            var scriptForEditPage = document.createElement('script');
            scriptForEditPage.setAttribute('src','./assets/js/edit.js');
            scriptForEditPage.setAttribute('id','edit-script');
            document.body.appendChild(scriptForEditPage);
        }

        if($('#directs').length){
            var scriptForDirectPage = document.createElement('script');
            scriptForDirectPage.setAttribute('src','./assets/js/direct.js');
            scriptForDirectPage.setAttribute('id','direct-script');
            document.body.appendChild(scriptForDirectPage);
        }
    });
</script>
</body>
</html>
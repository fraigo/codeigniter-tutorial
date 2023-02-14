<?php
 $value = $value ?: 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDY0IDY0IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2NCA2NDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCgkuc3Qwe2ZpbGw6IzRGNUQ3Mzt9DQoJLnN0MXtvcGFjaXR5OjAuMjt9DQoJLnN0MntmaWxsOiMyMzFGMjA7fQ0KCS5zdDN7ZmlsbDojRkZGRkZGO30NCjwvc3R5bGU+DQo8ZyBpZD0iTGF5ZXJfMSI+DQoJPGc+DQoJCTxjaXJjbGUgY2xhc3M9InN0MCIgY3g9IjMyIiBjeT0iMzIiIHI9IjMyIi8+DQoJPC9nPg0KCTxnIGNsYXNzPSJzdDEiPg0KCQk8Zz4NCgkJCTxwYXRoIGNsYXNzPSJzdDIiIGQ9Ik00My45LDQ3LjVjLTMuOC0xLjctNS4yLTQuMi01LjYtNi41YzIuOC0yLjIsNC45LTUuOCw2LjEtOS42YzEuMi0xLjYsMi0zLjIsMi00LjZjMC0xLTAuMy0xLjYtMS0yLjINCgkJCQljLTAuMi04LjEtNS45LTE0LjYtMTMtMTQuN2MtMC4xLDAtMC4xLDAtMC4yLDBjMCwwLDAsMC0wLjEsMGMtNy4xLDAtMTIuOCw2LjQtMTMuMSwxNC40Yy0wLjksMC41LTEuNCwxLjMtMS40LDIuNQ0KCQkJCWMwLDEuNiwxLDMuNiwyLjcsNS40YzEuMiwzLjMsMy4xLDYuNCw1LjUsOC40Yy0wLjQsMi4zLTEuNyw1LTUuNyw2LjhjLTIuMiwwLjktNi4xLDEuOC03LjgsMi42QzE2LjYsNTUsMjQuOSw1OCwzMS45LDU4bDAuMSwwDQoJCQkJYzAsMCwwLDAsMCwwYzcsMCwxNS4zLTMsMTkuNy03LjhDNTAsNDkuMyw0Ni4xLDQ4LjUsNDMuOSw0Ny41eiIvPg0KCQk8L2c+DQoJPC9nPg0KCTxnPg0KCQk8Zz4NCgkJCTxwYXRoIGNsYXNzPSJzdDMiIGQ9Ik00My45LDQ1LjVjLTMuOC0xLjctNS4yLTQuMi01LjYtNi41YzIuOC0yLjIsNC45LTUuOCw2LjEtOS42YzEuMi0xLjYsMi0zLjIsMi00LjZjMC0xLTAuMy0xLjYtMS0yLjINCgkJCQljLTAuMi04LjEtNS45LTE0LjYtMTMtMTQuN2MtMC4xLDAtMC4xLDAtMC4yLDBjMCwwLDAsMC0wLjEsMEMyNS4xLDgsMTkuNCwxNC40LDE5LDIyLjRjLTAuOSwwLjUtMS40LDEuMy0xLjQsMi41DQoJCQkJYzAsMS42LDEsMy42LDIuNyw1LjRjMS4yLDMuMywzLjEsNi40LDUuNSw4LjRjLTAuNCwyLjMtMS43LDUtNS43LDYuOGMtMi4yLDAuOS02LjEsMS44LTcuOCwyLjZDMTYuNiw1MywyNC45LDU2LDMxLjksNTZsMC4xLDANCgkJCQljMCwwLDAsMCwwLDBjNywwLDE1LjMtMywxOS43LTcuOEM1MCw0Ny4zLDQ2LjEsNDYuNSw0My45LDQ1LjV6Ii8+DQoJCTwvZz4NCgk8L2c+DQo8L2c+DQo8ZyBpZD0iTGF5ZXJfMiI+DQo8L2c+DQo8L3N2Zz4NCg==';
?>
<input id="<?=@$id?>" type="hidden" name="<?=@$name?>"  >
<div id="avatar_<?=@$id?>" class="avatar <?=@$class?>" 
    onclick="<?=htmlentities(@$onclick?:'')?>" value="<?=@$value?>"  
    style="background-image:url('<?=$value?>');width:<?=$width?:128?>px;height:<?=$height?:128?>px">
</div>
<style>
    .avatar{
        display: inline-block;
        border-radius: 99% 99%;
        border: 1px solid #eee;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }
    .avatar-rect{
        border-radius: 5px;
        background-size: contain;
    }
    .avatar-bg{
        background-color: rgba(128,128,128,0.5);
    }
</style>
<style type="text/css">
.btn-social {
    box-sizing: border-box;
    position: relative;
    padding: 10px 15px 10px 46px;
    border: none;
    text-align: left;
    line-height: 34px;
    white-space: nowrap;
    border-radius: 0.2em;
    font-size: 16px;
    font-weight: 500;
    color: #FFF;
}
.btn-social:before {
  content: "";
  box-sizing: border-box;
  position: absolute;
  top: 0;
  left: 0;
  width: 34px;
  height: 100%;
}
.btn-social:focus {
    outline: none;
}
.btn-social:active {
    box-shadow: inset 0 0 0 32px rgba(0,0,0,0.1);
}

/* Facebook */
.btn-facebook {
    background-color: #4C69BA;
    background-image: linear-gradient(#4C69BA, #3B55A0);
    text-shadow: 0 -1px 0 #354C8C;
}
.btn-facebook:before {
    border-right: #364e92 1px solid;
    background: url(../assets/front/images/facebook_icon.png) 6px 6px no-repeat;
}
.btn-facebook:hover,
.btn-facebook:focus {
    color: #fff;
    background-color: #5B7BD5;
    background-image: linear-gradient(#5B7BD5, #4864B1);
}
/* Google */
.btn-google {
    margin: 0.5em;
    background: #DD4B39;
}
.btn-google:before {
    border-right: #BB3F30 1px solid;
    background: url(../assets/front/images/google_icon.png) 6px 6px no-repeat;
}
.btn-google:hover,
.btn-google:focus {
    color: #fff;
    background: #E74B37;
}
</style>

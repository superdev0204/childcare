<!DOCTYPE html>
<html lang="en">

<head>
    @push('meta')
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1,maximum-scale=1,user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
    @endpush

    @push('link')
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,700" rel="stylesheet">
    @endpush

    @stack('meta')
    @stack('title')
    @stack('link')

    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
    <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
    <script async custom-element="amp-lightbox-gallery" src="https://cdn.ampproject.org/v0/amp-lightbox-gallery-0.1.js">
    </script>
    <script async custom-element="amp-addthis" src="https://cdn.ampproject.org/v0/amp-addthis-0.1.js"></script>
    <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
    <script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
    <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
    <script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>

    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            animation: -amp-start 8s steps(1, end) 0s 1 normal both
        }

        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-moz-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-ms-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-o-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }
    </style>
    <style amp-custom>
        @font-face {
            font-family: trebuchet_msregular;
            font-style: normal;
            font-weight: 400;
            src: url(font/trebuc-webfont.eot?) format("eot"),
                url(font/trebuc-webfont.woff) format("woff"),
                url(font/trebuc-webfont.ttf) format("truetype")
        }

        [role=button],
        label,
        ul#menu>li .menu-list {
            cursor: pointer
        }

        a,
        a:focus,
        a:hover,
        body,
        ul.submenu li a {
            text-decoration: none
        }

        li,
        ul,
        ul#menu>li,
        ul.submenu {
            list-style-type: none
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            color: inherit;
            font: inherit;
            margin: 0
        }

        button {
            overflow: visible
        }

        button,
        select {
            text-transform: none
        }

        button,
        html input[type=button],
        input[type=reset],
        input[type=submit] {
            -webkit-appearance: button;
            cursor: pointer
        }

        button[disabled],
        html input[disabled] {
            cursor: default
        }

        button::-moz-focus-inner,
        input::-moz-focus-inner {
            border: 0;
            padding: 0
        }

        input[type=checkbox],
        input[type=radio] {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 0
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            height: auto
        }

        input[type=search] {
            -webkit-appearance: textfield;
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box
        }

        input[type=search]::-webkit-search-cancel-button,
        input[type=search]::-webkit-search-decoration {
            -webkit-appearance: none
        }

        fieldset {
            border: 1px solid silver;
            margin: 0 2px;
            padding: .35em .625em .75em
        }

        table {
            border-collapse: collapse;
            border-spacing: 0
        }

        *,
        :after,
        :before {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box
        }

        html {
            font-size: 10px;
            -webkit-tap-highlight-color: transparent
        }

        body {
            line-height: 1.42857143
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit
        }

        a:focus {
            outline: -webkit-focus-ring-color auto 5px;
            outline-offset: -2px
        }

        a {
            color: #4f9a29;
        }

        .img-responsive {
            display: block;
            max-width: 100%;
            height: auto
        }

        .img-rounded {
            border-radius: 6px
        }

        .img-thumbnail {
            padding: 4px;
            line-height: 1.42857143;
            border: 1px solid #ddd;
            border-radius: 4px;
            -webkit-transition: all .2s ease-in-out;
            -o-transition: all .2s ease-in-out;
            transition: all .2s ease-in-out;
            display: inline-block;
            max-width: 100%;
            height: auto
        }

        .img-circle {
            border-radius: 50%
        }

        hr {
            margin-top: 20px;
            margin-bottom: 20px;
            border-top: 1px solid #eee
        }

        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: inherit;
            font-weight: 500;
            line-height: 1.1;
            color: inherit
        }

        .h1,
        .h2,
        .h3,
        h1,
        h2,
        h3 {
            margin-top: 20px;
            margin-bottom: 10px
        }

        .h4,
        .h5,
        .h6,
        h4,
        h5,
        h6 {
            margin-top: 10px;
            margin-bottom: 10px
        }

        .h1,
        h1 {
            font-size: 36px
        }

        .h2,
        h2 {
            font-size: 30px
        }

        .h3,
        h3 {
            font-size: 24px
        }

        .h4,
        h4 {
            font-size: 18px
        }

        .h5,
        h5 {
            font-size: 14px
        }

        .h6,
        h6 {
            font-size: 12px
        }

        p {
            margin: 0 0 10px
        }

        @media (min-width:768px) {
            .lead {
                font-size: 21px
            }

            .container {
                width: 750px
            }
        }

        .clear,
        .clearfix:after,
        .container-fluid:after,
        .container:after,
        .pie-wrapper:nth-child(3n+1),
        .pro_info span,
        .row:after {
            clear: both
        }

        .img-cnt,
        .ser-box,
        .ser-box h3 i,
        .ser-head {
            text-align: center
        }

        .container,
        .container-fluid {
            margin-right: auto;
            margin-left: auto;
            padding-left: 15px;
            padding-right: 15px
        }

        @media (min-width:992px) {
            .container {
                width: 970px
            }
        }

        @media (min-width:1200px) {
            .container {
                width: 1170px
            }
        }

        .row {
            margin-left: -15px;
            margin-right: -15px
        }

        .col-lg-1,
        .col-lg-10,
        .col-lg-11,
        .col-lg-12,
        .col-lg-2,
        .col-lg-3,
        .col-lg-4,
        .col-lg-5,
        .col-lg-6,
        .col-lg-7,
        .col-lg-8,
        .col-lg-9,
        .col-md-1,
        .col-md-10,
        .col-md-11,
        .col-md-12,
        .col-md-2,
        .col-md-3,
        .col-md-4,
        .col-md-5,
        .col-md-6,
        .col-md-7,
        .col-md-8,
        .col-md-9,
        .col-sm-1,
        .col-sm-10,
        .col-sm-11,
        .col-sm-12,
        .col-sm-2,
        .col-sm-3,
        .col-sm-4,
        .col-sm-5,
        .col-sm-6,
        .col-sm-7,
        .col-sm-8,
        .col-sm-9,
        .col-xs-1,
        .col-xs-10,
        .col-xs-11,
        .col-xs-12,
        .col-xs-2,
        .col-xs-3,
        .col-xs-4,
        .col-xs-5,
        .col-xs-6,
        .col-xs-7,
        .col-xs-8,
        .col-xs-9 {
            position: relative;
            min-height: 1px;
            padding-left: 15px;
            padding-right: 15px
        }

        .col-xs-1,
        .col-xs-10,
        .col-xs-11,
        .col-xs-12,
        .col-xs-2,
        .col-xs-3,
        .col-xs-4,
        .col-xs-5,
        .col-xs-6,
        .col-xs-7,
        .col-xs-8,
        .col-xs-9 {
            float: left
        }

        .col-xs-12 {
            width: 100%
        }

        .col-xs-11 {
            width: 91.66666667%
        }

        .col-xs-10 {
            width: 83.33333333%
        }

        .col-xs-9 {
            width: 75%
        }

        .col-xs-8 {
            width: 66.66666667%
        }

        .col-xs-7 {
            width: 58.33333333%
        }

        .col-xs-6 {
            width: 50%
        }

        .col-xs-5 {
            width: 41.66666667%
        }

        .col-xs-4 {
            width: 33.33333333%
        }

        .col-xs-3 {
            width: 25%
        }

        .col-xs-2 {
            width: 16.66666667%
        }

        .col-xs-1 {
            width: 8.33333333%
        }

        @media (min-width:768px) {

            .col-sm-1,
            .col-sm-10,
            .col-sm-11,
            .col-sm-12,
            .col-sm-2,
            .col-sm-3,
            .col-sm-4,
            .col-sm-5,
            .col-sm-6,
            .col-sm-7,
            .col-sm-8,
            .col-sm-9 {
                float: left
            }

            .col-sm-12 {
                width: 100%
            }

            .col-sm-11 {
                width: 91.66666667%
            }

            .col-sm-10 {
                width: 83.33333333%
            }

            .col-sm-9 {
                width: 75%
            }

            .col-sm-8 {
                width: 66.66666667%
            }

            .col-sm-7 {
                width: 58.33333333%
            }

            .col-sm-6 {
                width: 50%
            }

            .col-sm-5 {
                width: 41.66666667%
            }

            .col-sm-4 {
                width: 33.33333333%
            }

            .col-sm-3 {
                width: 25%
            }

            .col-sm-2 {
                width: 16.66666667%
            }

            .col-sm-1 {
                width: 8.33333333%
            }
        }

        @media (min-width:992px) {

            .col-md-1,
            .col-md-10,
            .col-md-11,
            .col-md-12,
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-5,
            .col-md-6,
            .col-md-7,
            .col-md-8,
            .col-md-9 {
                float: left
            }

            .col-md-12 {
                width: 100%
            }

            .col-md-11 {
                width: 91.66666667%
            }

            .col-md-10 {
                width: 83.33333333%
            }

            .col-md-9 {
                width: 75%
            }

            .col-md-8 {
                width: 66.66666667%
            }

            .col-md-7 {
                width: 58.33333333%
            }

            .col-md-6 {
                width: 50%
            }

            .col-md-5 {
                width: 41.66666667%
            }

            .col-md-4 {
                width: 33.33333333%
            }

            .col-md-3 {
                width: 25%
            }

            .col-md-2 {
                width: 16.66666667%
            }

            .col-md-1 {
                width: 8.33333333%
            }
        }

        @media (min-width:1200px) {

            .col-lg-1,
            .col-lg-10,
            .col-lg-11,
            .col-lg-12,
            .col-lg-2,
            .col-lg-3,
            .col-lg-4,
            .col-lg-5,
            .col-lg-6,
            .col-lg-7,
            .col-lg-8,
            .col-lg-9 {
                float: left
            }

            .col-lg-12 {
                width: 100%
            }

            .col-lg-11 {
                width: 91.66666667%
            }

            .col-lg-10 {
                width: 83.33333333%
            }

            .col-lg-9 {
                width: 75%
            }

            .col-lg-8 {
                width: 66.66666667%
            }

            .col-lg-7 {
                width: 58.33333333%
            }

            .col-lg-6 {
                width: 50%
            }

            .col-lg-5 {
                width: 41.66666667%
            }

            .col-lg-4 {
                width: 33.33333333%
            }

            .col-lg-3 {
                width: 25%
            }

            .col-lg-2 {
                width: 16.66666667%
            }

            .col-lg-1 {
                width: 8.33333333%
            }
        }

        @media (min-width: 768px) {
            .hidden-sm {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .hidden-xs {
                display: none;
            }
        }

        .clearfix:after,
        .clearfix:before,
        .container-fluid:after,
        .container-fluid:before,
        .container:after,
        .container:before,
        .row:after,
        .row:before {
            content: " ";
            display: table
        }

        .center-block {
            display: block;
            margin-left: auto;
            margin-right: auto
        }

        .pull-right {
            float: right
        }

        .pull-left {
            float: left
        }

        .hide {
            display: none
        }

        .show {
            display: block
        }

        .hidden,
        .visible-lg,
        .visible-lg-block,
        .visible-lg-inline,
        .visible-lg-inline-block,
        .visible-md,
        .visible-md-block,
        .visible-md-inline,
        .visible-md-inline-block,
        .visible-sm,
        .visible-sm-block,
        .visible-sm-inline,
        .visible-sm-inline-block,
        .visible-xs,
        .visible-xs-block,
        .visible-xs-inline,
        .visible-xs-inline-block {
            display: none
        }

        .invisible {
            visibility: hidden
        }

        .text-hide {
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0
        }

        .caption p,
        .italic {
            font-style: italic
        }

        .affix {
            position: fixed
        }

        body {
            font-family: 'trebuchet_msregular', sans-serif;
            color: #000;
            font-size: 14px
        }

        a:focus,
        a:hover {
            color: inherit
        }

        .header-icon-1 {
            background: 0 0;
            border: none;
            color: #fff;
            float: right;
            font-size: 17px;
            height: 30px;
            line-height: normal;
            margin: 10px 10px 0 0;
            padding: 0;
            width: 30px
        }

        .header-logo span {
            font-weight: 600;
            color: #47b475
        }

        img {
            max-width: 100%;
            width: auto
        }

        amp-sidebar {
            background-color: #fff;
            margin-top: 0;
            width: 350px
        }

        amp-sidebar ul.text-none {
            list-style: none;
            margin: 0;
            padding: 0;
            min-width: 220px
        }

        amp-sidebar ul.text-none li a {
            color: #fff;
            font-size: 15px;
            font-weight: 400;
            padding: 5px 20px
        }

        amp-sidebar ul.text-none li a:hover {
            color: #fff
        }

        .amp-close-btn {
            position: absolute;
            right: 10px;
            top: 5px
        }

        ul#menu {
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            color: #eee
        }

        ul#menu>li {
            position: relative;
            display: block;
            border-bottom: solid 1px rgba(255, 255, 255, .11)
        }

        ul#menu>li .menu-list {
            font-size: 13px;
            height: 55px;
            line-height: 55px;
            display: block;
            padding-left: 55px;
            position: relative
        }

        ul#menu>li>label>span {
            color: #FFF
        }

        ul#menu>li i {
            position: absolute;
            height: 55px;
            width: 55px;
            line-height: 55px
        }

        .cover-content,
        footer {
            width: 100%
        }

        .menu-list i {
            left: 0;
            font-size: 15px;
            color: #5f5f5f
        }

        label {
            position: relative;
            display: block;
            padding: 0 18px 0 12px;
            line-height: 3em;
            transition: background .3s
        }

        label:after {
            content: "";
            position: absolute;
            display: block;
            top: 50%;
            right: 22px;
            height: 0;
            border-top: 4px solid rgba(255, 255, 255, .5);
            border-bottom: 0 solid rgba(255, 255, 255, .5);
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            transition: border-bottom .1s, border-top .1s .1s
        }

        input:checked~label:after {
            border-top: 0 solid rgba(255, 255, 255, .5);
            border-bottom: 4px solid rgba(255, 255, 255, .5);
            transition: border-top .1s, border-bottom .1s .1s
        }

        input {
            display: none
        }

        input:checked~ul.submenu {
            max-height: 300px;
            -webkit-transition: all 1s ease-in-out;
            -moz-transition: all 1s ease-in-out;
            -o-transition: all 1s ease-in-out;
            transition: all 1s ease-in-out
        }

        ul.submenu {
            -webkit-transition: all 1s ease-in-out;
            -moz-transition: all 1s ease-in-out;
            -o-transition: all 1s ease-in-out;
            transition: all 1s ease-in-out
        }

        ul.submenu li a {
            color: #ddd;
            transition: background .3s;
            white-space: nowrap;
            border-top: solid 1px rgba(255, 255, 255, .13)
        }

        ul.submenu li a.menu-list i {
            font-size: 10px;
            margin-top: -1px
        }

        .gallery [class*=col-] {
            margin-bottom: 3%
        }

        .content-box {
            padding: 0 20px
        }

        footer {
            float: left
        }

        .copyright {
            color: #666;
            font-size: 13px;
            font-weight: 400;
            line-height: 27px;
            margin: 0
        }

        .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 20px;
            background-color: rgba(0, 0, 0, .75);
            color: #262c66
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        li,
        ul {
            padding: 0;
            margin: 0
        }

        .caption h4 {
            color: rgba(46, 204, 113, .75);
            font-weight: 800;
            text-transform: uppercase;
            margin: 0 0 3px
        }

        .caption p {
            margin: 0;
            color: rgba(46, 204, 113, .5);
            font-weight: 600
        }

        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-moz-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-ms-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-o-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        .header-logo,
        .learn_more_btn,
        .pro_info a i {
            float: none;
            display: table
        }

        .pro_info a i,
        i,
        label:after {
            font-family: FontAwesome;
            font-style: normal
        }

        header {
            position: fixed;
            background-color: #fff;
            width: 100%;
            z-index: 99999;
            box-shadow: 0 0 10px #ccc
        }

        body {
            background: #fff
        }

        button.menu-icon-1 {
            position: absolute;
            left: 12px;
            top: 0;
            background: no-repeat;
            border: 0;
            font-size: 38px;
            line-height: 63px
        }

        button.menu-icon-1 i {
            color: #fff
        }

        .footer_top_block p {
            color: #353942;
            font-size: 18px;
            line-height: 24px;
            font-weight: 300
        }

        footer {
            background-color: #fff;
            box-shadow: 2px 2px 10px #a9a9a9
        }

        .footer_inner {
            padding: 30px
        }

        a.footer-logo {
            margin: 0 auto 20px;
            float: none;
            display: table
        }

        input:checked~label,
        label:hover,
        ul.submenu {
            background: 0 0
        }

        ul#menu>li a.menu-list,
        ul#menu>li label.menu-list {
            cursor: pointer;
            display: block;
            position: relative;
            padding: 0;
            height: auto;
            line-height: 20px
        }

        ul#menu>li label.menu-list {
            font-size: 13px
        }

        ul#menu>li a.menu-list {
            color: #353942;
            font-size: 18px
        }

        ul#menu>li {
            padding: 15px;
            width: 100%
        }

        #menu label:after {
            border: 0;
            content: "\f107";
            font-size: 27px;
            color: #353942;
            line-height: 20px;
            position: absolute;
            top: 0
        }

        ul.submenu {
            max-height: 0;
            padding: 0;
            overflow: hidden;
            min-width: 100%
        }

        ul#menu>li ul.submenu a.menu-list {
            font-size: 14px;
            padding: 5px 10px
        }

        #menu input:checked~label:after,
        #menu label:hover {
            content: "\f106"
        }

        input:checked~ul.submenu {
            padding: 15px 0 0
        }

        input:checked~.menu-list:after {
            border-top: 0;
            border-bottom: 0;
            transition: inherit
        }

        .padding_header {
            padding-top: 75px
        }

        form.p2 {
            position: relative;
        }

        form.p2 p {
            margin: 0;
            font-size: 12px;
        }

        /* Amp Menu css*/
        amp-sidebar {
            background: #fff;
            width: 400px;
        }

        amp-sidebar .submenu {
            background: #fff;
            bottom: 0;
            box-shadow: 0 3px 20px 0 rgba(0, 0, 0, 0.075);
            left: 0;
            position: fixed;
            right: 0;
            top: 0;
            transform: translateX(100%);
            transition: transform 233ms cubic-bezier(0, 0, 0.21, 1)
        }

        amp-sidebar input:checked+.submenu {
            transform: translateX(0)
        }

        amp-sidebar input[type="checkbox"] {
            position: absolute;
            visibility: hidden
        }

        .menu-item {
            border-bottom: solid 1px #ededed;
            color: #363737;
            display: block;
            position: relative;
            text-transform: none;
        }

        .item-layer-1 {
            font-size: 20px;
            font-weight: 100;
            line-height: 25px;
            border-width: 0;
            padding: 0;
        }

        amp-sidebar .item-layer-1.has-sub-level {
            padding: 5px 25px 5px 25px;
            border-bottom: solid 1px #ededed;
        }

        a.menu-itemsub,
        a.menu-item {
            font-size: 16px;
            text-transform: none;
            color: #09357a;
            font-weight: 500;
            line-height: 47px;
            letter-spacing: 1px;
        }

        .logo_mobile {
            text-align: center;
            padding-top: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid #b4d496;
            float: left;
            width: 100%;
        }

        amp-sidebar .item-layer-2,
        amp-sidebar .item-layer-3 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
            line-height: 20px;
            padding: 5px 25px 5px 25px;
            float: left;
            line-height: 47px;
            width: 100%;
        }

        amp-sidebar .menu-layer::after {
            bottom: 0;
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            width: 13px
        }

        amp-sidebar .menu-layer .items {
            left: 0;
            right: 0
        }

        amp-sidebar .menu-layer.primary {
            height: 100%;
            position: relative
        }

        amp-sidebar .menu-layer.secondary {
            z-index: 2
        }

        amp-sidebar .menu-layer.tertiary {
            z-index: 3
        }

        amp-sidebar .menu-layer.secondary .items,
        amp-sidebar .menu-layer.tertiary .items {
            bottom: 0;
            top: 70px;
            border-top: solid 1px #ededed;
            position: absolute;
        }

        amp-sidebar .close-button {
            background: transparent;
            border: 0;
            height: 27px;
            position: absolute;
            right: 0px;
            top: 3px;
            width: 35px;
            z-index: 999;
            display: none;
        }

        amp-sidebar .close-button .fa {
            font-size: 38px;
            color: #262c66;
        }

        amp-sidebar .return-button {
            color: #09357a;
            font-weight: 700;
            left: 20px;
            position: absolute;
            top: 23px;
            font-size: 16px;
            line-height: 25px;
        }

        amp-sidebar .return-button::before {
            border: 0;
            content: '';
            display: inline-block;
            height: 11px;
            margin-right: 5px;
            position: relative;
            top: 1px;
            width: 12px;
            content: "\f053";
            font-family: FontAwesome;
            font-weight: normal;
        }

        amp-sidebar .has-sub-level::after {
            content: "\f054";
            font-family: FontAwesome;
            font-style: normal;
            font-weight: normal;
            text-decoration: inherit;
            color: #09357a;
            font-size: 14px;
            right: 15px;
            transform: rotate(0deg);
            top: 15px;
        }

        .menu-item.item-layer-2.has-sub-level::after {
            top: 10px;
        }

        .img-wid amp-img {
            float: left;
        }

        .img-wid span {
            float: left;
            line-height: 47px;
            padding-left: 0px;
        }

        .img-wid a {
            float: left;
            width: 100%;
        }

        #sidebar a {
            font-weight: bold;
        }

        .has-sub-level {
            float: left;
            width: 100%;
        }

        .color-cha {
            width: 18px;
            color: #09357a;
        }

        button#menu-button:focus {
            outline: none;
        }

        button.header-icon-1 {
            outline: none;
        }

        /*Top header*/
        button.header-icon-1 {
            display: none;
        }

        .top-info {
            float: left;
        }

        .top-info a {
            font-size: 13px;
            color: #4a4747;
        }

        .top-head {
            background: #f1f5f5;
            padding: 8px;
        }

        .top-menu ul li {
            list-style: none;
            display: inline-block;
            margin-left: 15px;
            float: left;
        }

        .top-menu ul {
            float: right;
        }

        .top-head li a {
            padding: 0px 5px;
            color: #000;
            float: left;
            font-size: 14px;
        }

        .header-logo {
            margin: 18px auto;
            float: left;
        }

        .second-head {
            border-bottom: 2px solid #b4d496;
        }

        .main-menu {
            padding: 15px 0px;
            float: right;
        }

        .main-menu ul li ul {
            display: none;
        }

        .main-menu li {
            display: inline-block;
            padding: 15px 15px;
            position: relative;
        }

        .main-menu li a {
            color: #09357A;
            font-size: 16px;
        }

        .main-menu li:hover ul {
            display: block;
            width: 190px;
            left: 0;
            position: absolute;
            box-shadow: 0px 0px 1px 2px #f5f5f5;
            background-color: #fff;
            border-radius: 3px;
            top: 50px;
            padding: 6px 0px;
        }

        .main-menu li ul li {
            padding: 0;
            width: 100%;
            border: 0px;
        }

        .main-menu ul li ul li a {
            width: 100%;
            float: left;
            font-size: 15px;
            padding: 5px 19px;
        }

        .nopadding {
            padding: 0;
        }

        /*Breaadcrum*/
        .breadcrumb-main {
            background: #eef5f9;
            padding-top: 65px;
            padding-bottom: 25px;
        }

        .breadcrumb li a {
            color: #468a23;
            font-size: 14px;
        }

        .breadcrumb li {
            display: inline-block;
            padding-right: 5px;
            color: #468a23;
            font-size: 14px;
        }

        .breadcrumb-wrap h1 {
            font-size: 28px;
            padding-bottom: 12px;
        }

        .breadcrumb-wrap h1 {
            font-size: 28px;
            color: #262c66;
        }

        .breadcrumb {
            padding-bottom: 15px;
        }

        .title-address {
            color: #666;
            font-size: 13px;
        }

        .title-address span {
            width: 100%;
            float: left;
            padding: 2px 0px 10px;
        }

        a.chk_avb_btn {
            padding: 4px 12px;
            font-size: 14px;
            font-weight: normal;
            line-height: 1.42857;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            background-image: none;
            border: 1px solid rgb(76, 174, 76);
            border-radius: 4px;
            color: rgb(255, 255, 255);
            background-color: rgb(92, 184, 92);
        }

        .title-address span .fa {
            padding-right: 8px;
        }

        .tit-rating {
            float: left;
            padding-top: 12px;
            font-style: 15px;
        }

        .tit-rating span {
            padding-left: 12px;
        }

        h5.sidebar_title {
            color: #262c66;
            font-size: 18px;
        }

        .social_share {
            margin-left: -10px;
            margin-top: -7px;
        }

        .tit-rating .fa {
            color: #ffb400;
            font-size: 15px;
        }

        /*End Breaadcrum*/
        /*Content*/
        .main-container {
            padding: 30px 0;
        }

        .main-wrap h3 {
            color: #262c66;
        }

        .desc {
            padding: 12px 0px;
        }

        .main-wrap h4 {
            padding-top: 20px;
        }

        p {
            color: #666;
            font-size: 14px;
            font-weight: 400;
            width: 100%;
            line-height: 23px
        }

        .main-wrap li {
            list-style-type: disc;
            line-height: 25px;
            font-size: 14px;
        }

        .main-wrap ul {
            padding-left: 16px;
            margin-top: 15px;
            padding-bottom: 15px;
        }

        .cont-table {
            border: 1px solid #eee;
            border-radius: 3px;
            margin: 20px 0px;
            float: left;
        }

        .content-spac {
            padding-top: 40px;
        }

        .con-wrap {
            padding: 8px 20px;
            line-height: 25px;
            border-bottom: 1px solid #eee;
            float: left;
            width: 100%;
        }

        .con-wrap span:first-child {
            width: 35%;
            color: #777;
            float: left;
        }

        .con-wrap span:last-child {
            width: 65%;
            float: left;
        }

        .content-four h3 {
            padding-bottom: 20px;
        }

        .table-for {
            padding: 20px 0px;
        }

        table.g-inspections {
            border-collapse: collapse;
            margin-bottom: 25px;
            width: 100%;
        }

        .table-for th {
            text-align: left;
            background: #d5e6f0;
            padding: 8px 15px;
            border: 1px solid #afd0e3;
        }

        .table-for td.divider {
            border: 0;
            padding: 5px;
        }

        .table-for td {
            text-align: left;
            padding: 8px 15px;
            border: 1px solid #afd0e3;
            background: #fbfcfd;
        }

        table.g-inspections .divider {
            border: none;
            background-color: transparent;
            padding: 5px;
        }

        .reviews-top {
            float: left;
            width: 100%;
            padding-bottom: 15px;
        }

        .reviews-left {
            float: left;
            width: 70%;
        }

        .reviews-right {
            float: right;
        }

        .reviews-right .fa {
            color: #ffb400;
            font-size: 15px;
        }

        .provider-logo {
            float: left;
            padding-right: 20px;
        }

        .reviews {
            padding-top: 40px;
        }

        .comment-body {
            border-bottom: 1px solid #eee;
            padding-bottom: 25px;
        }

        .rev-tit {
            font-weight: bold;
            padding-bottom: 4px;
        }

        .rev-date {
            color: #868e96;
        }

        .reviews-wrap {
            padding-top: 15px;
        }

        .review-section input {
            width: 100%;
            padding: 10px;
            display: block;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        .review-section select {
            width: 100%;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        .review-section textarea {
            width: 100%;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        a.btn.btn-review {
            background-color: #468A23;
            color: #FFF;
            padding: 10px 15px;
            font-size: 12px;
            display: inline-block;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .review-section label {
            padding-left: 0;
            font-size: 14px;
            line-height: 32px;
            padding-top: 15px;
        }

        .policy-main {
            padding-top: 20px;
        }

        .policy-main p {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .review-box {
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 20px 15px;
            margin-top: 30px;
        }

        .form-action input.btn {
            width: auto;
            float: left;
            background: #4f9a29;
            color: #fff;
            padding: 10px 26px;
            border-radius: 3px;
            margin-top: 15px;
        }

        .pro-wrap {
            float: left;
            padding-bottom: 15px;
            width: 33.33%;
        }

        .pro-wrap a {
            color: #09357A;
            float: left;
            clear: both;
        }

        .pro-wrap span {
            float: left;
            clear: both;
        }

        .providers h3 {
            padding-bottom: 30px;
        }

        .photo-galler figure {
            margin: 0;
            padding-bottom: 25px;
        }

        figcaption.image {
            display: none;
        }

        /**/
        .sidebar-main {
            background: #daeacb;
            padding: 25px;
        }

        .sidebar-contact li {
            list-style: none;
            padding-left: 25px;
            position: relative;
            line-height: 30px;
        }

        .sidebar-contact ul {
            padding-left: 0;
            position: relative;
            padding-bottom: 0px;
        }

        .sidebar-contact li .fa {
            color: #468A23;
            position: absolute;
            left: 0;
            top: 7px;
        }

        .sidebar-hour,
        .sidebar-note,
        .sidebar-quick {
            margin-top: 30px;
        }

        .side-tit {
            font-size: 18px;
            padding-bottom: 12px;
            color: #262c66;
        }

        .list-hou {
            border-bottom: 1px solid rgba(0, 0, 0, .1);
            padding: 12px 0;
            font-size: 14px;
        }

        .list-hou span:fist-child {
            float: left;
        }

        .list-hou span:last-child {
            float: right;
        }

        .list-hou:last-child {
            border: 0px;
        }

        .sidebar-note ol {
            padding-left: 20px;
        }

        .sidebar-note ol li {
            list-style: decimal;
            padding-bottom: 20px
        }

        .sidebar-note ol li:last-child {
            padding-bottom: 0px;
        }

        .sidebar-quick .quick-links a {
            padding: 7px 10px;
            border: 1px solid #468A23;
            color: #468A23;
            margin-bottom: 10px;
            width: 100%;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, .6);
            text-align: center;
            display: block;
        }

        .quick-links {
            margin-top: 15px;
        }

        /*Footer*/
        .footer_inner .footer-menu {
            padding-top: 0;
            width: 100%;
            text-align: center;
        }

        .footer-main {
            background: #f1f5f5;
        }

        ul.footer_link li {
            display: inline-block;
            padding: 0 20px;
            border-right: 2px solid #262c66;
            line-height: 16px;
            margin-bottom: 0px
        }

        ul.footer_link li:first-child {
            padding-left: 0;
        }

        ul.footer_link li:last-child {
            border-right: 0px;
        }

        .footer-menu {
            float: left;
            padding-top: 5px;
        }

        .social {
            float: right;
        }

        ul.footer_link li a {
            color: #262c66;
        }

        .social-icon {
            list-style: none;
            padding: 0px;
            width: 100%;
            margin: 0 0 0px;
            border-top: 1px solid rgba(255, 255, 255, .05);
            border-bottom: 1px solid rgba(255, 255, 255, .05)
        }

        .social-icon li {
            margin-left: 2px;
            margin-right: 2px;
            display: inline-block
        }

        .social-icon li a {
            height: 30px;
            line-height: 30px;
            width: 30px;
            display: block;
            background: #999;
            border-radius: 50%;
            text-align: center;
        }

        .social-icon li a i {
            color: #fff;
            font-size: 14px;
            padding-top: 8px;
        }

        .social-icon li {
            margin: 0 5px
        }

        .copyright-bott {
            border-top: 2px solid #b4d496;
            padding: 22px 0px;
        }

        .copyright-bott ul.footer_link li a {
            font-size: 14px;
            color: #666;
        }

        .copyright-bott ul.footer_link li {
            border-color: #666;
        }

        .copyright-bott p {
            font-size: 12px;
            line-height: 17px;
            margin-top: 8px;
            float: left;
        }

        .text-center.copyright {
            font-size: 12px;
            clear: both;
            text-align: center;
        }

        .breadcrumb li:after {
            content: ">>";
            padding-left: 6px;
        }

        .breadcrumb li:last-child:after {
            content: "";
        }

        .sidebar-main a {
            color: #4dac1b;
        }

        .table-for tr a {
            color: #09357A;
        }

        .providers>div {
            padding-left: 0px;
        }

        @media (max-width:991px) {
            .pro-wrap {
                width: 320px;
            }

            .main-menu {
                display: none;
            }

            button.header-icon-1 {
                display: block;
            }

            button.header-icon-1 {
                color: #262c66;
                font-size: 38px;
                padding-top: 0px;
                cursor: pointer
            }

            .header-logo {
                margin: 10px auto;
                padding-left: 12px;
            }

            .breadcrumb-main {
                padding-top: 40px;
                padding-bottom: 5px;
            }

            .footer-main {
                margin-top: 20px;
            }

            .mobile-login li {
                padding: 0px 5px;
                display: inline-block;
            }

            #sidebar .mobile-login li a {
                font-weight: normal;
                color: #09357a;
                font-size: 15px;
            }

            .mobile-login {
                padding-top: 10px;
            }

            .top-head {
                display: none;
            }

            .content-spac {
                padding-top: 40px;
                float: left;
                width: 100%;
            }
        }

        @media (max-width:640px) {
            .top-info {
                display: none;
            }

            .top-head {
                padding: 5px;
            }

            .breadcrumb-wrap h1 {
                font-size: 20px;
            }

            .provider-logo {
                padding-top: 15px;
            }

            .rev-img .provider-logo {
                padding-top: 0px;
            }

            .top-menu {
                width: 100%;
            }

            .top-menu ul {
                width: 100%;
                text-align: center;
            }

            .top-menu ul li {
                float: none;
                line-height: 8px;
            }

            .breadcrumb {
                float: left;
                width: 100%;
            }

            .breadcrumb li a,
            .breadcrumb li {
                font-size: 12px;
                float: left;
                line-height: 14px;
            }

            .main-container {
                padding: 0px 0px 0px 0px;
            }

            .breadcrumb-main {
                padding-top: 15px;
                padding-bottom: 5px;
            }

            ul.footer_link li {
                padding: 0 5px;
                line-height: 10px;
            }

            .footer_inner {
                padding: 15px;
            }

            .social-icon li a i {
                font-size: 12px;
                padding-top: 0px;
            }

            .social-icon li a {
                height: 25px;
                line-height: 25px;
                width: 25px;
            }

            ul.footer_link li a {
                font-size: 11px;
            }

            .footer-menu {
                padding-top: 10px;
            }

            .copyright-bott ul.footer_link li a {
                font-size: 12px;
            }

            .copyright-bott ul.footer_link li {
                float: left;
            }

            .copyright-bott p {
                margin-top: 15px;
            }

            .copyright-bott {
                padding: 15px 0px;
            }

            .padding_header {
                padding-top: 65px;
            }

        }

        /*Amp css end*/
    </style>
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>
</head>

<body>
    <amp-analytics type="googleanalytics">
        <script type="application/json">
        {
          "vars": {
            "account": "UA-11548587-3"
          },
          "triggers": {
            "trackPageview": {
              "on": "visible",
              "request": "pageview"
            }
          }
        }
        </script>
    </amp-analytics>
    <header>
        <div class="top-head">
            <div class="container">
                <div class="row">
                    <div class="top-info">
                        <!-- <a href="">test@test.com</a> | <a href="">+1 987654321</a> -->
                    </div>
                    <div class="top-menu">
                        <ul>
                            <?php if(isset($user->caretype)):?>
                            <li><a href="/user/logout" title="Log Out">Logout <?php echo $user->firstname; ?></a></li>
                            <?php else: ?>
                            <li><a href="/user/login">Login</a></li>
                            <li><a href="/user/new">Signup</a></li>
                            <?php endif;?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="second-head">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-xs-10">
                        <a href="/" class="header-logo" title="Childcare Center, Preschools, and Home Daycare">
                            <amp-img src="{{  asset('images/logo.png') }}" width="208" height="40" class="logo"
                                alt="Childcare Centers, Home Daycare, Child Development Center" noloading></amp-img>
                        </a>
                    </div>
                    <div class="col-md-8 col-xs-2">
                        <button class="header-icon-1" on="tap:sidebar.open"><i class="fa fa-navicon"></i></button>
                        <div class="main-menu">
                            <ul class="clearfix">
                                <li>
                                    <a href="/search">Find Providers</a>
                                    <ul>
                                        <li><a href="/state" title="Childcare Center, Child Development Centers, Preschools">Childcare
                                                Center</a></li>
                                        <li><a href="/homecare" title="Home Daycare, Group Home Day Care, Family Child Care">Home
                                                Daycare</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="/resources/">More Resources</a>
                                    <ul>
                                        <li><a href="/resources/" title="Child Care Resources">Childcare Resources</a>
                                        </li>
                                        <li><a href="/classifieds" title="Child Care Classified">Miscellaneous</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="/provider">Manage Listing</a>
                                    <ul>
                                        <li><a href="/provider" title="Add New Listing">Add Free Listing</a></li>
                                        <li><a href="/provider" title="Update Listing">Update Listing</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="/about">About</a>
                                    <ul>
                                        <li>
                                            <a href="/contact" title="Contact Child Care Center">Contact</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-------header ends----->
    <div class="padding_header"></div>
    <amp-sidebar id="sidebar" layout="nodisplay" side="right">
        <form class="menu-layer primary" action="/" target="_top" method="GET">
            <button type="reset" class="close-button user-valid valid" id="menu-button" on="tap:sidebar.toggle">
                <svg aria-hidden="true" data-prefix="fal" data-icon="times" role="img"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"
                    class="color-cha svg-inline--fa fa-times fa-w-10 fa-3x">
                    <path fill="currentColor"
                        d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z"
                        class=""></path>
                </svg>
            </button>
            <div class="logo_mobile">
                <a href="/" class="menu_logo">
                    <amp-img src="images/logo-child.png" width="208" height="40"></amp-img>
                </a>
                <div class="mobile-login">
                    <ul>
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Signup</a></li>
                    </ul>
                </div>
            </div>
            <div class="items">
                <label class="menu-item item-layer-1 has-sub-level">
                    <a href="https://childcarecenter.us/search" title="Find Providers" class="menu-itemsub">Find
                        Providers</a><input type="checkbox">
                    <div class="submenu menu-layer secondary">
                        <div class="return-button">Back</div>
                        <button type="reset" class="close-button" id="menu-button" on='tap:sidebar.toggle'></button>
                        <div class="items">
                            <a class="menu-item item-layer-2" href="https://childcarecenter.us/state">Childcare
                                Center</a>
                            <a class="menu-item item-layer-2" href="https://childcarecenter.us/homecare">Home
                                Daycare</a>
                            <a class="menu-item item-layer-2" href="https://nanny.us/">Nanny/Sitters</a>
                        </div>
                    </div>
                </label>
                <label class="menu-item item-layer-1 has-sub-level">
                    <a href="https://childcarecenter.us/resources/" title="More Resources" class="menu-itemsub">More
                        Resources</a><input type="checkbox">
                    <div class="submenu menu-layer secondary">
                        <div class="return-button">Back</div>
                        <button type="reset" class="close-button" id="menu-button"
                            on='tap:sidebar.toggle'></button>
                        <div class="items">
                            <a class="menu-item item-layer-2" href="https://childcarecenter.us/classifieds">Childcare
                                Resources</a>
                            <a class="menu-item item-layer-2"
                                href="https://childcarecenter.us/classifieds">Miscellaneous</a>
                            <a class="menu-item item-layer-2" href="https://childcarecenter.us/jobs">Child Care Jobs</a>
                        </div>
                    </div>
                </label>
                <label class="menu-item item-layer-1 has-sub-level">
                    <a href="https://childcarecenter.us/provider" title="Manage Listing" class="menu-itemsub">Manage
                        Listing</a><input type="checkbox">
                    <div class="submenu menu-layer secondary">
                        <div class="return-button">Back</div>
                        <button type="reset" class="close-button" id="menu-button"
                            on='tap:sidebar.toggle'></button>
                        <div class="items">
                            <a class="menu-item item-layer-2" href="https://childcarecenter.us/classifieds">Add Free
                                Listing</a>
                            <a class="menu-item item-layer-2" href="https://childcarecenter.us/provider">Update
                                Listing</a>
                        </div>
                    </div>
                </label>
                <label class="menu-item item-layer-1 has-sub-level">
                    <a href="https://childcarecenter.us/provider" title="Manage Listing" class="menu-itemsub">Manage
                        Listing</a><input type="checkbox">
                    <div class="submenu menu-layer secondary">
                        <div class="return-button">Back</div>
                        <button type="reset" class="close-button" id="menu-button"
                            on='tap:sidebar.toggle'></button>
                        <div class="items">
                            <a class="menu-item item-layer-2" href="/jobs">Find Jobs</a>
                            <a class="menu-item item-layer-2" href="/resumes">Find Resumes</a>
                            <a class="menu-item item-layer-2" href="/jobs/new">Post Job</a>
                            <a class="menu-item item-layer-2" href="/resumes/new">Post Resume</a>
                        </div>
                    </div>
                </label>
                <label class="menu-item item-layer-1 img-wid">
                    <input type="checkbox">
                    <a class="menu-item item-layer-2" href="https://childcarecenter.us/about"><span>About</span></a>
                    <a class="menu-item item-layer-2"
                        href="https://childcarecenter.us/contact"><span>Contact</span></a>
                </label>
            </div>
        </form>
    </amp-sidebar>

    @yield('content')

    <!----------footer--------------->
    <footer class="footer-main clearfix">
        <div class="footer_inner">
            <div class="container">
                <div class="footer-menu">
                    <ul class="footer_link">
                        <li><a href="/feedback">Feedback</a></li>
                        <li><a href="/provider" title="Add and Update Free Listing">Add / Update Listings</a></li>
                        <li><a href="/wesupport">Support</a></li>
                        <li><a href="/faqs" title="Frequently Asked Questions">FAQs</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="copyright-bott">
            <div class="container">
                <div class="col-md-12 nopadding">
                    <div class="footer-menu">
                        <ul class="footer_link">
                            <li><a href="/privacy">Privacy Policy</a></li>
                            <li><a href="/rss">RSS</a></li>
                        </ul>
                    </div>
                    <div class="social">
                        <ul class="social-icon clearfix">
                            <li><a href="https://www.facebook.com/childcarecenter" class="facebook-bg"><i
                                        class="fa fa-facebook"></i></a></li>
                            <li><a href="https://twitter.com/childcareUS" class="twitter-bg"><i
                                        class="fa fa-twitter"></i></a></li>
                            <li><a href="#" class="twitter-bg"><i class="fa fa-pinterest"></i></a></li>
                        </ul>
                    </div>
                </div>
                <p>Disclaimer: We at ChildcareCenter strive daily to keep our listings accurate and up-to-date, and to
                    provide top-level, practical information that you can use and trust. However, ChildcareCenter.us
                    does not endorse or recommend any of the childcare providers listed on its site, cannot be held
                    responsible or liable in any way for your dealings with them, and does not guarantee the accuracy of
                    listings on its site. We provide this site as a directory to assist you in locating childcare
                    providers in your area. We do not own or operate any child care facility, and make no representation
                    of any of the listings contained within ChildcareCenter.us.</p>
                <div class="text-center copyright">© <?php echo date('Y'); ?> Child Care Center US</div>
            </div>
        </div>
    </footer>
    <!----------footer ends---------->
</body>

</html>

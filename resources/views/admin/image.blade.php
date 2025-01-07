@extends('layouts.app_old')

@section('content')
<div id="content" class="clearfix">
    <div id="right">
        <div class="widget">
            Hi, <?php echo $user->firstname ?: $user->email ?>
            <a href="/user/logout">Sign Out</a>
        </div>
        @include('admin.right_panel')
    </div>
    <div id="left">
        <a href="/">Home</a>  &gt;&gt; <a href="/admin">Admin Home</a>  &gt;&gt; Images Statistics
        <h2>New Images</h2>
        <table width="100%">
            <tr>
                <th>Name</th>
                <th>Image</th>
            </tr>
            <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Image $image */
            foreach ($images as $image): ?>
            <tr class="d<?php echo $i % 2; $i++; ?>">
                <td width="40%">
                    <a target="_blank" href="/provider/view?id=<?php echo optional(optional($image)->provider)->id ?>"><?php echo $image->imagename?></a><br/>
                    <?php echo $image->altname ?><br/>
                    <?php echo $image->type ?><br/>
                    <?php if ($image->approved == 0): ?>
                        <form method="post" action="/admin/image/approve">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $image->id?>"/>
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER["REQUEST_URI"] ?>"/>
                            <input type="submit" name="update" value="Approve"/>
                        </form>
                        <form method="post" action="/admin/image/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $image->id?>"/>
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER["REQUEST_URI"] ?>"/>
                            <input type="submit" name="update" value="Not Approve"/>
                        </form>
                        <form method="post" action="/admin/image/delete">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $image->id?>"/>
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER["REQUEST_URI"] ?>"/>
                            <input type="submit" value="Delete"/>
                        </form>
                    <?php endif;?>
                </td>
                <td >
                    <img src="<?php echo env('IDRIVE_BITBUCKET_URL') ?>/<?php echo $image->imagename ?>" border="0" width="200" height="150" alt="<?php echo $image->altname ?>"
                    onClick="window.open('<?php echo env('IDRIVE_BITBUCKET_URL') ?>/<?php echo $image->imagename ?>','mywindow','width=640,height=480,scrollbars=no,location=no')" style="cursor:pointer;"/>
                </td>
            </tr>
            <?php endforeach;?>
        </table><br />
    </div>
    </div>
@endsection

@push('title')
    <title>Upload Your Images</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/provider?pid=<?php echo $provider->id; ?>">Providers</a> &gt;&gt; </li>
                <li>Upload Childcare Logo and Pictures<br /></li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>test-Upload Logo, Images, and Documents - <?php echo $provider->name; ?></h1>

            <h2>Please use the tool below to upload your child care logo and pictures.</h2>
            <p>You can upload 2 images at a time. You can have up to 12 photos.</p>
            <?php if (isset($message)) :?>
            <p><?php echo $message; ?></p>
            <?php endif;?>

            <form method="post" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <dl class="zend_form">
                    <dt id="logo-label"><label for="logo">Logo:</label></dt>
                    <dd id="logo-element">
                        <input type="file" id="logo" name="logo">
                        @error('logo')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="image1-label"><label for="image1">Upload Image #1:</label></dt>
                    <dd id="image1-element">
                        <input type="file" id="image1" name="image1">
                        @error('image1')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="image1Alt-label"><label for="image1Alt">Image #1 Alternate Name:</label></dt>
                    <dd id="image1Alt-element">
                        <input id="image1Alt" name="image1Alt">                        
                    </dd>
                    <dt id="image2-label"><label for="image2">Upload Image #2:</label></dt>
                    <dd id="image2-element">
                        <input type="file" id="image2" name="image2">
                        @error('image2')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="image2Alt-label"><label for="image2Alt">Image #2 Alternate Name:</label></dt>
                    <dd id="image2Alt-element">
                        <input id="image2Alt" name="image2Alt">
                    </dd>
                    <dt id="uploadImage-label">&nbsp;</dt>
                    <dd id="uploadImage-element"><input type="submit" name="submit" value="Upload Images"></dd>
                </dl>
            </form>
            <br />
            <?php if($provider->logo) : ?>
            <h2>Below is the current logo for your child care:</h2>
            <img width="160" height="142" src="<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $provider->logo;?>" border="0" /><br />
            <?php endif;?>
            <br />
            <?php if(count($images)): ?>
            <h2>Below are the images you have uploaded:</h2>
            <table width="500">
                <?php
                /** @var \Application\Domain\Entity\Image $image */
                foreach ($images as $image): ?>
                <?php if(($image->type != 'LOGO' && $image->approved >= 0) || ($image->type == 'LOGO' && $image->approve == 0)): ?>
                <tr>
                    <td width="300">
                        <img src="<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename;?>" border="0" width="160" height="142" max-width="100%"
                            alt="<?php echo $image->altname; ?>"
                            onClick="window.open('<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename;?>','mywindow','width=640,height=480,scrollbars=no,location=no')"
                            style="cursor:pointer;" />
                    </td>
                    <td valign="center">
                        <?php if($image->type == 'LOGO'):?>
                        TO BE USED FOR LOGO - PENDING APPROVAL
                        <?php elseif($image->approved == 0):?>
                        PENDING APPROVAL
                        <?php endif;?>
                        <br /><br /><a href="/provider/imagedelete?id=<?php echo $image->id; ?>&pid=<?php echo $image->provider_id; ?>">Remove
                            this Image</a><br />
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </table>
            <?php endif;?>

        </section>
        <!---------right container------>
        <section class="right-sect">

            Hello, <strong><?php echo $user->firstname ?: $user->email; ?></strong>

            <div class="listSidebar">
                <h2>Actions</h2>
                <a class="btn m-t-10" href="/provider/update?pid=<?php echo $provider->id; ?>">Update Daycare Information</a>

                <?php if ($user && $user->multi_listings) :?>
                <a class="btn m-t-10" href="/provider/find">Find Your Childcare</a>
                <?php endif;?>

                <a class="btn m-t-10" href="/provider/imageupload?pid=<?php echo $provider->id; ?>">Upload Logo and Images</a>
                <a class="btn m-t-10" href="/provider/update-operation-hours?pid=<?php echo $provider->id; ?>">Update Operation
                    Hours</a>
                <a class="btn m-t-10" href="/reviews/view?pid=<?php echo $provider->id; ?>">View Review History</a>
                <a class="btn m-t-10" href="/inspection/view?pid=<?php echo $provider->id; ?>">View Inspection History</a>
                <a class="btn m-t-10" target="blank" href="/jobs/newjob">Post Your Job Requirements</a>
                <a class="btn m-t-10" href="/user/logout">Sign Out</a>

            </div>

            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <ol>
                        <?php if($provider->approved == 0): ?>
                        <li>This daycare has not been approved yet. Please allow 2-3 business days for review.</li>
                        <?php elseif($provider->approved < 0): ?>
                        <li>Your daycare is not approved for listing on our website.</li>
                        <?php else:?>
                        <li>Your daycare profile page has been visited <?php echo $provider->visits; ?> times.</li>
                        <?php endif;?>
                    </ol>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select the form and file input fields
            var form = document.getElementById('uploadForm');
            var logoInput = document.getElementById('logo');
            var image1Input = document.getElementById('image1');
            var image2Input = document.getElementById('image2');
    
            // Add submit event listener to the form
            form.addEventListener('submit', function(event) {
                // Validate logo file size
                if (logoInput.files.length > 0 && logoInput.files[0].size > {{ (env('UPLOAD_MAX_FILESIZE') * 1024 * 1024) }}) { // 128MB in bytes
                    var divElement = document.getElementById('logo-element').querySelector('div');
                    var newElement = document.createElement('div');
                    newElement.textContent = 'Logo file size exceeds the limit of 128MB.';
                    
                    if(divElement){
                        document.getElementById('logo-element').removeChild(divElement);
                    }
                    document.getElementById('logo-element').appendChild(newElement)
                    event.preventDefault(); // Prevent form submission
                    return;
                }
    
                // Validate image1 file size
                if (image1Input.files.length > 0 && image1Input.files[0].size > {{ (env('UPLOAD_MAX_FILESIZE') * 1024 * 1024) }}) { // 128MB in bytes
                    var divElement = document.getElementById('image1-element').querySelector('div');
                    var newElement = document.createElement('div');
                    newElement.textContent = 'Image #1 file size exceeds the limit of 128MB.';
                    if(divElement){
                        document.getElementById('image1-element').removeChild(divElement);
                    }
                    document.getElementById('image1-element').appendChild(newElement)
                    event.preventDefault(); // Prevent form submission
                    return;
                }
    
                // Validate image2 file size
                if (image2Input.files.length > 0 && image2Input.files[0].size > {{ (env('UPLOAD_MAX_FILESIZE') * 1024 * 1024) }}) { // 128MB in bytes
                    var divElement = document.getElementById('image2-element').querySelector('div');
                    var newElement = document.createElement('div');
                    newElement.textContent = 'Image #2 file size exceeds the limit of 128MB.';
                    if(divElement){
                        document.getElementById('image2-element').removeChild(divElement);
                    }
                    document.getElementById('image2-element').appendChild(newElement)
                    event.preventDefault(); // Prevent form submission
                    return;
                }
            });
        });
    </script>
@endsection

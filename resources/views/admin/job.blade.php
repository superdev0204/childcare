@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            <div class="widget">
                Hi, <?php echo $user->firstname ?: $user->email; ?>
                <a href="/user/logout">Sign Out</a>
            </div>
            @include('admin.right_panel')
        </div>
        <div id="left">
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Jobs and Resumes Statistics
            <h2>New Job Posting</h2>
            <table width="100%">
                <tr>
                    <th>Job</th>
                    <th>Description</th>
                    <th>Requirements</th>
                    <th>Approve?</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Job $job */
            foreach ($jobs as $job): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="30%">
                        <a target="_blank" href="/jobs/jobdetail?id=<?php echo $job->id; ?>"><?php echo $job->title . ' - ' . $job->company; ?></a><br />
                        <?php echo $job->city . ', ' . $job->state . ' ' . $job->zip; ?><br />
                        <?php echo $job->email_verified == 1 ? 'Email Verified' : 'Not Verified'; ?>
                    </td>
                    <td>
                        <?php echo $job->description; ?>
                    </td>
                    <td>
                        <?php echo $job->requirements; ?>
                    </td>
                    <td>
                        <form method="post" action="/admin/job/approve">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $job->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Approve" />
                        </form>
                        <form method="post" action="/admin/job/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $job->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Not Approve" />
                        </form>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
            <h2>New Resumes</h2>
            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>Objective</th>
                    <th>Experience</th>
                    <th>Approved?</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Resume $resume */
            foreach ($resumes as $resume): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="30%">
                        <a target="_blank" href="/jobs/resume?id=<?php echo $resume->id; ?>"><?php echo $resume->name . ' - ' . $resume->position; ?></a><br />
                        <?php echo $resume->created; ?>
                        <?php echo $resume->email_verified == 1 ? 'Email Verified' : 'Not Verified'; ?>
                    </td>
                    <td>
                        <?php echo $resume->objective; ?>
                    </td>
                    <td>
                        <?php echo $resume->experience; ?>
                    </td>
                    <td>
                        <form method="post" action="/admin/resume/approve">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $resume->id; ?>" />
                            <input type="hidden" name="type" value="RESUME" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Approve" />
                        </form>
                        <form method="post" action="/admin/resume/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $resume->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Not Approve" />
                        </form>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
        </div>
    </div>
@endsection

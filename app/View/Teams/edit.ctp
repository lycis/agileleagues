<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title"><strong>Edit Team</strong></div>
			</div>
			<div class="panel-body">
				<?= $this->Bootstrap->create('Team'); ?>
				<?= $this->Bootstrap->hidden('id'); ?>
				<?= $this->Bootstrap->input('name', array('type' => 'text', 'class' => 'form-control')); ?>
				<br/>
				<button type="submit" class="btn btn-lg btn-success">Save</button>
				<?= $this->Bootstrap->end(); ?>
			</div>
		</div>
	</div>
</div>

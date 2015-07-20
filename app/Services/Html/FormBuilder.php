<?php namespace App\Services\Html;

class FormBuilder extends \Collective\Html\FormBuilder {

	public function submit($value = null, $options = [])
	{
		return sprintf('
			<div class="form-group %s">
				%s
			</div>',
			empty($options) ? '' : $options[0],
			parent::submit($value, ['class' => 'btn btn-default'])
		);
	}

	public function destroy($text, $message, $class = null)
	{
		return parent::submit($text, ['class' => 'btn btn-danger btn-block ' . ($class? $class:''), 'onclick' => 'return confirm(\'' . $message . '\')']);
	}

	public function control($type, $colonnes, $nom, $errors, $label = null, $valeur = null, $pop = null, $placeholder = '')
	{
		$attributes = ['class' => 'form-control', 'placeholder' => $placeholder];
		return sprintf('
			<div class="form-group %s %s">
				%s
				%s
				%s
				%s
			</div>',
			($colonnes == 0)? '': 'col-lg-' . $colonnes,
			$errors->has($nom) ? 'has-error' : '',
			$label ? $this->label($nom, $label, ['class' => 'control-label']) : '',
			$pop? '<a href="#" tabindex="0" class="badge pull-right" data-toggle="popover" data-trigger="focus" title="' . $pop[0] .'" data-content="' . $pop[1] . '"><span>?</span></a>' : '',
			call_user_func_array(['Form', $type], ($type == 'password')? [$nom, $attributes] : [$nom, $valeur, $attributes]),
			$errors->first($nom, '<small class="help-block">:message</small>')
		);
	}

	public function check($name, $label)
	{
		return sprintf('
			<div class="checkbox col-lg-12">
				<label>
					%s%s
				</label>
			</div>',
			parent::checkbox($name),
			$label
		);		
	}

	public function checkHorizontal($name, $label, $value)
	{
		return sprintf('
			<div class="form-group">
				<div class="checkbox">
					<label>
						%s%s
					</label>
				</div>
			</div>',
			parent::checkbox($name, $value),
			$label
		);		
	}

	public function selection($nom, $list = [], $selected = null, $label = null)
	{
		return sprintf('
			<div class="form-group" style="width:200px;">
				%s
				%s
			</div>',
			$label ? $this->label($nom, $label, ['class' => 'control-label']) : '',
			parent::select($nom, $list, $selected, ['class' => 'form-control'])
		);
	}

}

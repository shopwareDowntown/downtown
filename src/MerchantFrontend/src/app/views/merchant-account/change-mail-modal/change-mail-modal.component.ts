import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordValidators } from '../../../shared/validators/password.validator';

@Component({
  selector: 'portal-change-mail-modal',
  templateUrl: './change-mail-modal.component.html',
  styleUrls: ['./change-mail-modal.component.scss']
})
export class ChangeMailModalComponent implements OnInit {

  emailForm: FormGroup;
  @Input() modalOpen = false;
  @Output() emailChanged = new EventEmitter<string>();
  private initialFormState: any;

  constructor(private readonly formBuilder: FormBuilder) { }

  ngOnInit(): void {
    this.emailForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      repeatEmail: ['', [Validators.required, Validators.email]]
    }, {validator: Validators.compose([PasswordValidators.matchEmail])});
    this.initialFormState = this.emailForm.value;
  }

  modalClosed() {
    this.modalOpen = false;
    this.emailForm.reset(this.initialFormState)
  }

  changeMail() {
    this.emailChanged.emit(this.emailForm.get('email').value);
    this.modalClosed();
  }
}

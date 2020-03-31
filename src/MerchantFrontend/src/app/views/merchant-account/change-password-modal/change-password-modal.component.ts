import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordValidators } from '../../../shared/validators/password.validator';

@Component({
  selector: 'portal-change-password-modal',
  templateUrl: './change-password-modal.component.html',
  styleUrls: ['./change-password-modal.component.scss']
})
export class ChangePasswordModalComponent implements OnInit {

  passwordForm: FormGroup;
  @Input() modalOpen = false;
  @Output() passwordChanged = new EventEmitter<string>();
  private initialFormState: any;

  constructor(private readonly formBuilder: FormBuilder) { }

  ngOnInit(): void {
    this.passwordForm = this.formBuilder.group({
      password: ['', [Validators.required, Validators.minLength(8)]],
      repeatPassword: ['', [Validators.required, Validators.minLength(8)]]
    }, {validator: Validators.compose([PasswordValidators.matchPassword])});
    this.initialFormState = this.passwordForm.value;
  }

  modalClosed() {
    this.modalOpen = false;
    this.passwordForm.reset(this.initialFormState)
  }

  changeMail() {
    this.passwordChanged.emit(this.passwordForm.get('password').value);
    this.modalClosed();
  }
}

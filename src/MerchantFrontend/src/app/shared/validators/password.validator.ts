import { FormGroup, ValidationErrors } from '@angular/forms';

export class PasswordValidators {
  static matchPassword(form: FormGroup): ValidationErrors|null {
    const password = form.get('password').value;
    const passwordRepeat = form.get('repeatPassword').value;
    if (password !== passwordRepeat) {
      return {passwordMismatch: true};
    }
    return null;
  }
}

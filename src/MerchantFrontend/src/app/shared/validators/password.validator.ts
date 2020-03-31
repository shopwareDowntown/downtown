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

  static matchEmail(form: FormGroup): ValidationErrors|null {
    const email = form.get('email').value;
    const repeatEmail = form.get('repeatEmail').value;
    if (email !== repeatEmail) {
      return {emailMismatch: true}
    }
  }
}

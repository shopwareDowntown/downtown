import { Component, Input, OnInit } from '@angular/core';
import {Router} from '@angular/router';
import { LoginService } from '../../core/services/login.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'portal-merchant-login',
  templateUrl: './merchant-login.component.html',
  styleUrls: ['./merchant-login.component.scss']
})
export class MerchantLoginComponent implements OnInit {

  isLogging: boolean;
  loginFailed = false;
  @Input() loginModalOpen: boolean;
  loginForm: FormGroup;
  private initialFormState: any;

  constructor(
    private readonly router: Router,
    private readonly loginService: LoginService,
    private readonly formBuilder: FormBuilder
  ) { }

  ngOnInit(): void {
    this.initializeForm();
  }

  enterLogin($event: KeyboardEvent) {
    // listen for enter key to login
    if ($event.code === 'Enter') {
      this.doLogin();
    }
  }

  public doLogin() {
    this.loginFailed = false;
    this.loginService.login(this.loginForm.get('username').value, this.loginForm.get('password').value)
      .subscribe((result) => {
        this.modalClosed();
        this.router.navigate(['/merchant/profile']);

      },() => {
        this.loginFailed = true;
      });
  }

  initializeForm(): void {
    this.loginForm = this.formBuilder.group({
      username: ['', Validators.required],
      password: ['', Validators.required]
    });
    this.initialFormState = this.loginForm.value;
  }

  modalClosed(): void {
    this.loginForm.reset();
    this.loginModalOpen = false;
  }
}

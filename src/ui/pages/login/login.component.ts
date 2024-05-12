import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ReactiveFormsModule, FormsModule, FormGroup, FormControl } from '@angular/forms';
import { ApiClient, ApplicationState } from '../../services';
import { UserInformation } from '../../models';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  loginForm = new FormGroup({
    username: new FormControl(''),
    password: new FormControl(''),
  });

  constructor(private apiClient: ApiClient, private router: Router, private applicationState: ApplicationState){}

  onLogin(){
    const username = this.loginForm.get('username')?.value as string;
    const password = this.loginForm.get('password')?.value as string;

    if(username == undefined ||password == undefined)
      return;

    this.apiClient.loginUser(username, password).subscribe(
      value => {
          this.applicationState.User = new UserInformation(value.token);
          this.router.navigateByUrl("/");
      }
  );
  }
}

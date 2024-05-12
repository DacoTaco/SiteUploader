import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ApplicationState } from '../../services';

@Component({
  selector: 'app-logout',
  standalone: true,
  template: '',
  imports: [],
})

export class LogoutComponent {
  constructor(private applicationState: ApplicationState, private router: Router)
  {
    applicationState.User = null;
    router.navigateByUrl('/login');
  }
}

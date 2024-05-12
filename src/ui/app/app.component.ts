import { Component } from '@angular/core';
import { Router, RouterOutlet } from '@angular/router';
import { ApplicationState } from '../services'

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  constructor(authenticationState: ApplicationState, router: Router) {
    if (authenticationState.isAuthenticated()) {
      router.navigate(['login']);
    }
  }
  title = 'DacoTaco\'s 1337 file uploader y0';
}

import { Component } from '@angular/core';
import { NgFor } from '@angular/common';
import { RouterLink } from '@angular/router'
import { ApiClient, ApplicationState } from '../../services';
import { FileEntry, UserType } from '../../models';


@Component({
  selector: 'file-entries',
  standalone: true,
  imports: [NgFor, RouterLink],
  templateUrl: './file-entries.component.html',
  styleUrl: './file-entries.component.css'
})

export class FileEntriesComponent {
  public files: FileEntry[] = [];
  public applicationState: ApplicationState;

  constructor(private apiClient: ApiClient, private state: ApplicationState) 
  { 
    this.applicationState = state;
  }

  isUserGuest():boolean
  {
    return this.applicationState.User?.userRole == UserType.Guest;
  }
  
  isUserAdmin():boolean
  {
    return this.applicationState.User?.userRole == UserType.Admin;
  }

  ngOnInit()
  {
    this.apiClient.getFiles().subscribe((response) => {
      this.files = response;
    });
  }
}

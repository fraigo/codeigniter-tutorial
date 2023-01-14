<header class="header">
    <div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h2>{title}</h2>
        <div>
            <button type="button" class="btn" onclick="window.history.back(-1)" >Back</button>
            <button class="btn btn-primary" onclick="document.location=this.getAttribute('data-href')" data-href="{editurl}">Edit</button>
        </div>
    </div>
    {item}
    <div class="form-item">
        <label>Id</label>
        <div>{id}</div>
    </div>
    <div class="form-item">
        <label>Name</label>
        <div>{name}</div>
    </div>
    <div class="form-item">
        <label>Email</label>
        <div>{email}</div>
    </div>
    <div class="form-item">
        <label>User Type</label>
        <div>{user_type_name}</div>
    </div>
    <div class="form-item">
        <label>Last Login</label>
        <div>{login_at|default(N/A)}</div>
    </div>
    <div class="form-item">
        <label>Last Update</label>
        <div>{updated_at}</div>
    </div>
    {/item}
    </div>
</header>

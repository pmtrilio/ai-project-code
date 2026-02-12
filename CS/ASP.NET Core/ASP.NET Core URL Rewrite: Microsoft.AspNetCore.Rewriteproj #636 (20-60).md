    <Reference Include="Microsoft.AspNetCore.Http.Features" />
    <Reference Include="Microsoft.AspNetCore.Routing" />
    <Reference Include="Microsoft.AspNetCore.Routing.Abstractions" />
    <Reference Include="Microsoft.Extensions.Configuration.Abstractions" />
    <Reference Include="Microsoft.Extensions.FileProviders.Abstractions" />
    <Reference Include="Microsoft.Extensions.Logging.Abstractions" />
    <Reference Include="Microsoft.Extensions.Options" />
  </ItemGroup>

  <ItemGroup>
    <Compile Include="$(SharedSourceRoot)Reroute.cs" />
  </ItemGroup>

  <ItemGroup>
    <InternalsVisibleTo Include="Microsoft.AspNetCore.Rewrite.Tests" />
  </ItemGroup>
</Project>

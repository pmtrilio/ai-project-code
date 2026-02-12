//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

#endregion

using System.Diagnostics;
using Grpc.Core;
#if SUPPORT_LOAD_BALANCING
using Grpc.Net.Client.Balancer;
using Grpc.Net.Client.Balancer.Internal;
#endif
using Grpc.Net.Client.Configuration;
using Grpc.Net.Client.Internal;
using Grpc.Net.Client.Internal.Retry;
using Grpc.Net.Compression;
using Grpc.Shared;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Logging.Abstractions;

namespace Grpc.Net.Client;

/// <summary>
/// Represents a gRPC channel. Channels are an abstraction of long-lived connections to remote servers.
/// Client objects can reuse the same channel. Creating a channel is an expensive operation compared to invoking
/// a remote call so in general you should reuse a single channel for as many calls as possible.
/// </summary>
[DebuggerDisplay("{DebuggerToString(),nq}")]
public sealed partial class GrpcChannel : ChannelBase, IDisposable
{
    internal const int DefaultMaxReceiveMessageSize = 1024 * 1024 * 4; // 4 MB
#if SUPPORT_LOAD_BALANCING
    internal const long DefaultInitialReconnectBackoffTicks = TimeSpan.TicksPerSecond * 1;
    internal const long DefaultMaxReconnectBackoffTicks = TimeSpan.TicksPerSecond * 120;
#endif
    internal const int DefaultMaxRetryAttempts = 5;
    internal const long DefaultMaxRetryBufferSize = 1024 * 1024 * 16; // 16 MB
    internal const long DefaultMaxRetryBufferPerCallSize = 1024 * 1024; // 1 MB
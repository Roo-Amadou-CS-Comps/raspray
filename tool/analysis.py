import time
import subprocess
from memory_profiler import memory_usage

def run_program(type, output_runtime):
    commands = [
        ["hydra", "-L", "usernames.txt", "-P", "passwords.txt", "18.188.93.218", "http-get", "/important/SECRETCODES.html"],
        ["./raspray_test", "-u", "usernames.txt", "-p", "passwords.txt", "-i", "18.188.93.218"]
    ]
    # Measure runtime
    start_time = time.time()
    process = subprocess.Popen(commands[type], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    stdout, stderr = process.communicate()
    end_time = time.time()
    runtime = end_time - start_time
    output_runtime[0] = runtime

    # Print command output
    """print("Results from running", commands[type][0], ":")
    print("Output:", stdout.decode())
    print("Error Output:", stderr.decode())
    print(f"Runtime of {commands[type][0]} command: {runtime:.2f} seconds")
    """

# Measure memory usage
def profile_memory_usage(num):
    """Profiles memory usage of the Hydra command."""
    runtime = [0]
    mem_usage = memory_usage((run_program, (num, runtime)), max_iterations=10, interval=0.05)
    peak_memory = max(mem_usage)
    """print(f"Peak Memory Usage: {peak_memory:.2f} MB")"""
    if runtime[0] > 5:
        print(f"Runtime of the command is {runtime[0]:.2f} seconds, which is greater than 5 seconds. Waiting for 5 seconds before running the next command to let server cool down...")
        time.sleep(5)
    return runtime[0], peak_memory

# Execute the memory profiling and runtime measurement

def write_results(totalRuns, avgHYDRA, avgRASP, avgmemHYDRA, avgmemRASP, hydratimes, rasptimes, hydratimesOutliers, rasptimesOutliers, memHYDRAs, memRASPs):

    # Write the results to a file
    with open("results.txt", "w") as f:
        f.write(f"Attempted to run the commands {totalRuns} times each.\n")
        f.write("Results of the analysis:\n\n")
        f.write(f"Average runtime for Hydra command: {avgHYDRA}\n")
        f.write(f"Average runtime for Raspray command: {avgRASP}\n")
        f.write(f"Average memory usage for Hydra command: {avgmemHYDRA}\n")
        f.write(f"Average memory usage for Raspray command: {avgmemRASP}\n")
        f.write("\n")
        f.write(f"All runtimes for Hydra command: {hydratimes}\n")
        f.write(f"All runtimes for Raspray command: {rasptimes}\n")
        f.write("\n")
        f.write(f"Outliers for Hydra command: {hydratimesOutliers}\n")
        f.write(f"Outliers for Raspray command: {rasptimesOutliers}\n")
        f.write("\n")
        f.write("Memory usage for Hydra command:\n")
        f.write(str(memHYDRAs))
        f.write("\n")
        f.write("Memory usage for Raspray command:\n")
        f.write(str(memRASPs))


if __name__ == "__main__":
    totalRuns = 500
    tottimeHYDRA = 0
    tottimeRASP = 0
    totmemHYDRA = 0
    totmemRASP = 0

    hydratimes = []
    rasptimes = []

    hydratimesWithoutOutliers = []
    rasptimesWithoutOutliers = []
    rasptimesOutliers = []
    hydratimesOutliers = []

    memHYDRAs = []
    memRASPs = []

    for i in range(1, totalRuns+1):
        print(f"\n\nRound {i}:\n\nRunning hydra command...")
        runtimeHYDRA, memHYDRA = profile_memory_usage(0)
        hydratimes.append(runtimeHYDRA)
        memHYDRAs.append(memHYDRA)
        tottimeHYDRA += runtimeHYDRA
        totmemHYDRA += memHYDRA

        print("Waiting for 10 seconds before running the next command to let server cool down...")
        time.sleep(10)

        print("Running raspray command...")
        runtimeRASP, memRASP = profile_memory_usage(1)
        rasptimes.append(runtimeRASP)
        memRASPs.append(memRASP)
        tottimeRASP += runtimeRASP
        totmemRASP += memRASP

        if runtimeHYDRA < 5:
            hydratimesWithoutOutliers.append(runtimeHYDRA)
        else:
            hydratimesOutliers.append(runtimeHYDRA)

        if runtimeRASP < 5:
            rasptimesWithoutOutliers.append(runtimeRASP)
        else:
            rasptimesOutliers.append(runtimeRASP)

        if i < totalRuns - 1:
            if i % 10 == 0:
                print("Writing results to file...")
                write_results(i, tottimeHYDRA / i, tottimeRASP / i, totmemHYDRA / i, totmemRASP / i, hydratimes, rasptimes, hydratimesOutliers, rasptimesOutliers, memHYDRAs, memRASPs)
            if  i % 100 == 0:
                print("Waiting for 45 seconds before running the next run to let server cool down...")
                time.sleep(45)
            else:
                print("Waiting for 10 seconds before running the next run to let server cool down...")
                time.sleep(10)
        

    avgHYDRA = tottimeHYDRA / totalRuns
    avgRASP = tottimeRASP / totalRuns
    avgmemHYDRA = totmemHYDRA / totalRuns
    avgmemRASP = totmemRASP / totalRuns

    print("\n")
    print(f"Attempted to run the commands {totalRuns} times each.")
    print("Results of the analysis:")
    print("\n")
    print("Average runtime for Hydra command:", avgHYDRA)
    print("Average runtime for Raspray command:", avgRASP)
    print("Average memory usage for Hydra command:", avgmemHYDRA)
    print("Average memory usage for Raspray command:", avgmemRASP)
    print("\n")
    print("All runtimes for Hydra command:", hydratimes)
    print("All runtimes for Raspray command:", rasptimes)
    print("\n")
    print("All runtimes for Hydra command without outliers:", hydratimesWithoutOutliers)
    print("All runtimes for Raspray command without outliers:", rasptimesWithoutOutliers)
    print("\n")
    print("Outliers for Hydra command:", hydratimesOutliers)
    print("Outliers for Raspray command:", rasptimesOutliers)
    print("\n")
    print("Memory usage for Hydra command:")
    print(memHYDRAs)
    print("\n")
    print("Memory usage for Raspray command:")
    print(memRASPs)

    write_results(totalRuns, avgHYDRA, avgRASP, avgmemHYDRA, avgmemRASP, hydratimes, rasptimes, hydratimesOutliers, rasptimesOutliers, memHYDRAs, memRASPs)


    
        
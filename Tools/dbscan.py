import sys
import numpy as np

from sklearn.cluster import DBSCAN
from sklearn import metrics
from sklearn.datasets.samples_generator import make_blobs
from sklearn.preprocessing import StandardScaler
from scipy.spatial import distance

##############################################################################
# Read Turker data file
def read_turk_data(filename):
    data = {}
    f = open(filename)
    lines = f.readlines()
    for line in lines:
        split_str = line.split('\t\t')
        #print split_str
        datum = {}
        datum["id"] = split_str[0]
        datum["workerid"] = split_str[1]
        datum["vid"] = split_str[2][:-4] # e.g., "s1_c02_v02" and "s04" from "s1_c02_v02_s04"
        datum["labels"] = parse_labels(datum["workerid"], int(split_str[2][-2:]), split_str[3].rstrip('\n'))
        if datum["vid"] not in data:
            data[datum["vid"]] = []
        for label in datum["labels"]:
            data[datum["vid"]].append(label)
    f.close()
    return data

##############################################################################
# Read ground truth file
# not used anymore, because clustering doesn't really use ground truth.
def read_truth_data(filename):
    data = {}
    f = open(filename)
    lines = f.readlines()
    for line in lines:
        split_str = line.split('\t\t')
        #print split_str
        datum = {}
        datum["id"] = split_str[0]
        datum["vid"] = split_str[1]
        datum["labels"] = parse_labels("", 1, split_str[2].rstrip('\n'))
        #print datum
        if datum["vid"] not in data:
            data[datum["vid"]] = []
        for label in datum["labels"]:
            data[datum["vid"]].append(label)
    f.close()
    # print data
    return data

##############################################################################
# Parse labels
def parse_labels(workerid, seq, str):
    labels = []
    #print str
    split_str = str.split('\t')
    #print split_str
    for v in split_str:
        if v == '': continue
        split_v = v.split('@')
        label = {}
        label["workerid"] = workerid
        label["time"] = (seq-1)*54 + float(split_v[0])
        label["desc"] = split_v[1]
        labels.append(label)
    return labels

##############################################################################
# Generate sample data
#centers = [[1, 1], [-1, -1], [1, -1]]
#X, labels_true = make_blobs(n_samples=20, n_features=1, cluster_std=0.4, random_state=0)

def format_data_old(label_list):
    #data = [[10.67, 18, 51, 9, 51, 10.2, 19, 51],]
    # print label_list
    data = [label_list,]

    X = np.empty( shape=(len(label_list), 1) )
    X = np.transpose(data)
    #print X
    # print X.shape
    X = StandardScaler().fit_transform(X)
    print X
    return X


global_label_list = []
global_label_list_desc = []
std = 0
stop_words_list = [
"a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the"
]

def get_filtered_label(line):
    word_list = [word for word in line.split()]
    final_list = []
    for word in word_list:
        if word not in stop_words_list:
            final_list.append(word)
    return " ".join(final_list)


# check if two labels should be considered identical
def is_same_label(label1, label2):
    # noop cannot be same with non-noop
    if (label1 == "noop" and label2 != "noop") or (label1 != "noop" and label2 == "noop"):
        return [False, "", "noop", 0]
    if label1 == label2:
        return [True, label1, "identical", 1]
    l1 = label1.lower()
    l2 = label2.lower()
    if l1 == l2:
        return [True, l1, "case", 1]
    fl1 = get_filtered_label(l1)
    fl2 = get_filtered_label(l2)
    if get_filtered_label(l1) == get_filtered_label(l2):
        return [True, get_filtered_label(l1), "stopword", 1]
    # TODO: string comparison, sentence analysis
    # import difflib
    # sim_score = difflib.SequenceMatcher(None, fl1,fl2).ratio()
    import jellyfish
    sim_score = jellyfish.jaro_winkler(fl1, fl2)
    if sim_score >= 0.5:
        # print "fuzzy", sim_score,
        # print "[fuzzy]", sim_score, l1, "===", l2  # fl1, "===", fl2, "|||",
        final_label = max([l1, l2], key=len)
        return [True, final_label, "sim", sim_score]
    # print "diff", sim_score, l1, "===", l2
    return [False, "", "", sim_score]


# check if two labels should be considered identical
def text_similarity(label1, label2):

    # -1 is the same, 1 is totally different
    # (0, 1) becomes inverted (-1, 1)
    if label1 == label2:
        return 0
    l1 = label1.lower()
    l2 = label2.lower()
    if l1 == l2:
        return 0
    fl1 = get_filtered_label(l1)
    fl2 = get_filtered_label(l2)
    if get_filtered_label(l1) == get_filtered_label(l2):
        return 0
    # TODO: string comparison, sentence analysis
    # import difflib
    # sim_score = difflib.SequenceMatcher(None, fl1,fl2).ratio()
    import jellyfish
    sim_score = jellyfish.jaro_winkler(fl1, fl2)
    return 1 - sim_score

# u, v are indices!
def distance_metric(uu, vv):
    u = int(uu[0])
    v = int(vv[0])

    # dist = np.sqrt(((u-v)**2).sum())
    # 1d mahalanobis
    time_dist = np.abs(global_label_list[u]-global_label_list[v]) / std
    # print u, v, dist
    w_time = float(weight)
    w_label = 1 - w_time
    label1 = global_label_list_desc[u]
    label2 = global_label_list_desc[v]
    label_dist = text_similarity(label1, label2)
    # print u, v, w_time, time_dist, w_label, label_dist, label1, "====", label2
    # print time_dist, label_dist
    w = w_time * time_dist + w_label * label_dist
    # print w
    return w


def format_data(label_list, label_list_desc):
    #data = [[10.67, 18, 51, 9, 51, 10.2, 19, 51],]
    data = [[i for i in xrange(len(label_list))], ]
    # print label_list
    # data = [label_list,]
    # print data
    # original form
    # X = np.empty( shape=(len(label_list), 1) )
    X = np.transpose(data)
    # #print X
    # print X.shape
    # X = StandardScaler().fit_transform(X)

    # taking into accout the labels

    global std
    time_data = [label_list, ]
    T = np.transpose(time_data)
    std = T.std()

    global global_label_list
    global global_label_list_desc
    global_label_list = label_list
    global_label_list_desc = label_list_desc
    X = distance.squareform(distance.pdist(X, distance_metric))
    # print X
    X = StandardScaler().fit_transform(X)
    # print X.shape
    # np.set_printoptions(threshold=np.nan)
    # print X
    return X

##############################################################################
# Compute DBSCAN
def run_dbscan(X, label_turk_list, epsilon):
    db = DBSCAN(eps=epsilon, min_samples=2, metric="euclidean").fit(X)
    core_samples = db.core_sample_indices_
    labels = db.labels_

    # Number of clusters in labels, ignoring noise if present.
    n_clusters_ = len(set(labels)) - (1 if -1 in labels else 0)    
    #print labels
    # print core_samples
    # print('Estimated number of clusters: %d' % n_clusters_)
    # print("Homogeneity: %0.3f" % metrics.homogeneity_score(label_turk_list, labels))
    # print("Completeness: %0.3f" % metrics.completeness_score(label_turk_list, labels))
    # print("V-measure: %0.3f" % metrics.v_measure_score(label_turk_list, labels))
    # print("Adjusted Rand Index: %0.3f"
    #       % metrics.adjusted_rand_score(label_turk_list, labels))
    # print("Adjusted Mutual Information: %0.3f"
    #       % metrics.adjusted_mutual_info_score(label_turk_list, labels))
    # print("Silhouette Coefficient: %0.3f"
    #       % metrics.silhouette_score(X, labels))
    # print labels
    new_labels = [str(i) for i in labels]
    # print new_labels

    # plot_results(X, labels, core_samples, n_clusters_)
    return new_labels

##############################################################################
# Plot result

def plot_results(X, labels, core_samples, n_clusters_):
    import pylab as pl

    # Black removed and is used for noise instead.
    unique_labels = set(labels)
    colors = pl.cm.Spectral(np.linspace(0, 1, len(unique_labels)))
    for k, col in zip(unique_labels, colors):
        if k == -1:
            # Black used for noise.
            col = 'k'
            markersize = 6
        class_members = [index[0] for index in np.argwhere(labels == k)]
        cluster_core_samples = [index for index in core_samples
                                if labels[index] == k]
        for index in class_members:
            x = X[index]
            if index in core_samples and k != -1:
                markersize = 14
            else:
                markersize = 6
            pl.plot(x[0], x[1], 'o', markerfacecolor=col,
                    markeredgecolor='k', markersize=markersize)

    pl.title('Estimated number of clusters: %d' % n_clusters_)
    pl.show()


# "separate": Group unclustered (id is -1) into time brackets
def group_unclustered(clusters, label_turk_list):
    # clone because original cluster names will be updated
    clone_clusters = list(clusters)
    new_cluster_count = 0
    tally = []
    for (i, label) in enumerate(label_turk_list):
        key = str(clone_clusters[i])
        # print i, key, label
        if key == "-1.0":
            tally.append(label)
            new_cid = key + "_T" + str(new_cluster_count)
            clusters[i] = new_cid
        else:
            if len(tally) > 0:
                new_cluster_count += 1
                # print "creating new cluster", tally
            tally = []
    # if len(tally) > 0:
    #     new_cluster_count += 1                
    #     print "creating new cluster", tally
    # tally = []
    return  clusters

# "reclustering": Avoid multiple labels from a Turker within a cluster
def recluster(clusters, label_turk_list, sorted_turk):
    # clone because original cluster names will be updated
    clone_clusters = list(clusters)
    current_cluster = "no cluster yet"
    for (i, label) in enumerate(label_turk_list):
        key = str(clone_clusters[i])

        if current_cluster != key:  # new cluster beginning
            turkers = []
            new_cluster_count = 0
            current_cluster = key
        # print key, current_cluster, new_cluster_count
        tid = sorted_turk[i]["workerid"]
        if tid in turkers:                    
            new_cid = current_cluster + "_R" + str(new_cluster_count)
            new_cluster_count += 1
            turkers = []
            # print "new cluster created", new_cid
            # update all upcoming labels in this cluster with new_cid
            for (j, label_inner) in enumerate(label_turk_list):
                if i <= j and clone_clusters[j] == current_cluster:
                    clusters[j] = new_cid
                    # sorted_turk[j]["new_cluster"] = new_cid

        turkers.append(tid)
        # print label, tid, key, sorted_turk[i]["desc"]
    return clusters


def get_cluster_centroid(key, clusters, label_turk_list):
    vals = []
    for (i, cid) in enumerate(clusters):
        if key == cid:
            # print label_turk_list[i]
            vals.append(label_turk_list[i])
    # print "centroid for", key, np.mean(vals)
    return np.mean(vals)


# "neighbor": Check neighbors' text labels to adjust clusters
def merge_neighbors(clusters, label_turk_list, label_turk_list_desc, sorted_turk):
    threshold = 10 # only consider neighbor labels within 10 second distance
    clone_clusters = list(clusters)
    current_cluster = "no cluster yet"
    for (i, label) in enumerate(label_turk_list):
        if i == 0:  # starting from index 1, because it's about merging i and i-1
            continue
        key = str(clone_clusters[i])

        if current_cluster != key:  # new cluster beginning
            new_cluster_count = 0
            current_cluster = key                
        # if key != "-1.0": # looking at only valid clusters (this will now include _R and _T)
        try:
            # skip if i-1 is in the same cluster as i.
            # an existing cluster doesn't need merging.
            if key == str(clone_clusters[i-1]):
                continue
            import math
            # print "\n", i, (i-1), "in cluster", clone_clusters[i-1], label_turk_list[i], label_turk_list[i-1]                        
            dist = math.fabs(label_turk_list[i] - label_turk_list[i-1])
            if i == len(label_turk_list) - 1:
                next_dist = 10000
            else:
                next_dist = math.fabs(label_turk_list[i] - label_turk_list[i+1])

            if dist > threshold:
                continue

            similarity = is_same_label(label_turk_list_desc[i], label_turk_list_desc[i-1])
            # to make sure i-1 is more similar to i than to i-2, both in terms of time and label
            if i == len(label_turk_list) - 1:
                next_similarity = [False, "", "", 0]
            else:
                next_similarity = is_same_label(label_turk_list_desc[i], label_turk_list_desc[i+1])
            # check distance between i and i-1, and between i and i+1
            # in order for merge between i and i-1 to happen, i and i-1 should be closer
            # print "[testing...]", similarity[3], next_similarity[3], dist, next_dist
            similarity_cond = similarity[0] and similarity[3] > next_similarity[3] and dist < next_dist
            if not similarity_cond:
                # print "[not similar]", similarity[1]
                continue
            
            # check if i-1's cluster contains labels by i's Turker, which violates reclustering
            tid = sorted_turk[i]["workerid"]
            tid_exists = False
            for (j, label_inner) in enumerate(label_turk_list):
                # only checking i-1's cluster
                if i >= j and clone_clusters[j] == clone_clusters[i-1]:
                    if sorted_turk[j]["workerid"] == tid:
                        tid_exists = True
                        break
            if tid_exists:
                # print "[cannot merge]", "multiple labels from", tid
                continue
            
            print "[merge]", i, (i-1), label_turk_list_desc[i], "===", label_turk_list_desc[i-1], dist, next_dist
            # case by case based on whether items are singletons or not
            prev_key = str(clone_clusters[i-1])
            is_singleton = clone_clusters.count(key) == 1
            prev_is_singleton = clone_clusters.count(prev_key) == 1
            if is_singleton and prev_is_singleton:
                clone_clusters[i] = clone_clusters[i-1] # either way works
            elif not is_singleton and prev_is_singleton:
                clone_clusters[i-1] = clone_clusters[i]
            elif is_singleton and not prev_is_singleton:
                clone_clusters[i] = clone_clusters[i-1]
            else: 
            # dealing with clusters on both sides.
            # merge to the cluster whose centroid is closer to i
                print "[clusters both sides]", clone_clusters[i], clone_clusters[i-1]
                cent = get_cluster_centroid(key, clone_clusters, label_turk_list)
                prev_cent = get_cluster_centroid(prev_key, clone_clusters, label_turk_list)
                if math.fabs(label_turk_list[i] - cent) >= math.fabs(label_turk_list[i] - prev_cent):
                    clone_clusters[i] = clone_clusters[i-1]
                else:
                    clone_clusters[i-1] = clone_clusters[i]         

        except IndexError: # ignore
            print "index error", i

    return clone_clusters


# Get the representative time for a cluster
# Consider using the minimum value not the mean
def getClusterTime(cluster_data, cluster_id):
    total_time = 0
    count = 0
    min_time = 1000000
    #print cluster_data
    for item in cluster_data:
        #print item
        if (cluster_id == item["cluster_id"]):
            total_time += item["time"]
            count = count + 1
            if min_time > item["time"]:
                min_time = item["time"]
    if count == 0:
        return -1

    # the mean
    return total_time / count
    # the minimum
    # return min_time


# Save JSON files of final clusters
def save_files(turk_filename, final_data, eps):
    import json
    with open("./data/" + turk_filename + '.new.merge.w' + weight + '.' + str(eps) + '.final.json', 'wb') as fp:
        #json.dump(final_data, fp)
        json.dump(final_data, fp, sort_keys=True, indent=4, separators=(',', ': '))


''' Usage 
        python dbscan.py [turk_filename] [weight_time]
    Parameters
    - turk_filename: e.g., s1_c.data
    - weight_time: value between 0 and 1, indicating weight put to time vs text similarity
    - (not used) truth_filename: e.g., s1_c.truth
'''
turk_filename = sys.argv[1]
# truth_filename = sys.argv[2]
weight = sys.argv[2]
# read and parse data
turk = read_turk_data(turk_filename)
# truth = read_truth_data(truth_filename)
vidlist = turk.keys()
vidlist.sort()

for i in range(1, 2):
    eps = i * 0.07
    final_data = {}
    for vid in vidlist:
        # if vid != "s1_c05_v05": #"s1_c05_v03" "s1_c03_v01": #
        #     continue
        # print "\n" 
        sorted_turk = sorted(turk[vid], key=lambda tup: tup["time"])

        # for label in turk[vid]:
        #     print label["time"], label["desc"]
        # print "SORTED:"
        # for label in sorted_turk:
        #     print label["time"], label["desc"]

        # print vid, "Turk:", len(turk[vid]), "Truth:", len(truth[vid])#, data[vid]
        # only grab time and desc information from the data and put into separate lists for easier processing
        label_turk_list = []
        label_turk_list_desc = []
        # label_truth_list = []
        # for label in turk[vid]:
        for label in sorted_turk:
            # print label["time"], label["desc"]
            label_turk_list.append(label["time"])
            label_turk_list_desc.append(label["desc"])
        # for label in truth[vid]:
        #     label_truth_list.append(label["time"])
        # label_turk_list.sort()
        # label_truth_list.sort()
        # format for DBSCAN
        
        X = format_data(label_turk_list, label_turk_list_desc)
        #labels_truth = label_truth_list #format_data(label_truth_list)
        # now run DBSCAN
        clusters = run_dbscan(X, label_turk_list, eps)
        # print "DBSCAN"
        # for (i, label) in enumerate(label_turk_list):
        #     print i, label, "(", clusters[i], ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]
        
        clusters = group_unclustered(clusters, label_turk_list)
        # print "GROUP "
        # for (i, label) in enumerate(label_turk_list):
        #     print i, label, "(", clusters[i], ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]        
        
        clusters = recluster(clusters, label_turk_list, sorted_turk)
        # print "RECLUS"
        # for (i, label) in enumerate(label_turk_list):
        #     print i, label, "(", clusters[i], ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]        
        
        clusters = merge_neighbors(clusters, label_turk_list, label_turk_list_desc, sorted_turk)
        # print "MERGE "
        # for (i, label) in enumerate(label_turk_list):
        #     print i, label, "(", clusters[i], ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]        



        # Print results and store them in a file for each eps
        header = "=== " + vid + " eps=" + str(eps) + " w_time=" + str(weight) + " ===\n"
        fp = open("./data/" + turk_filename + '.w' + weight + '.parsed', 'a+b')
        fp.write(header)
        print header
        cluster_data = {}
        for (i, label) in enumerate(label_turk_list):
            key = str(clusters[i])
            entry = {'cluster_id': key, 'time': label, 'workerid': sorted_turk[i]["workerid"], 'desc': sorted_turk[i]["desc"]}
            if key not in cluster_data:
                cluster_data[key] = []
            cluster_data[key].append(entry)
            line = str(label) + " (" + clusters[i] + ") " + sorted_turk[i]["workerid"] + " " + sorted_turk[i]["desc"]
            fp.write(line + "\n")
            # print i, label, "(", clusters[i], ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]

        final_data[vid] = {}
        for (i, label) in enumerate(label_turk_list):
            cid = str(clusters[i])
            final_data[vid][cid] = {'cluster_id': cid, 'time': getClusterTime(cluster_data[cid], cid), 'points_turk': cluster_data[cid]}

        save_files(turk_filename, final_data, eps)


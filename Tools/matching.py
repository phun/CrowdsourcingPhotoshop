#!/usr/bin/python
# ecole polytechnique - c.durr - 2009

# Kuhn-Munkres, The hungarian algorithm.  Complexity O(n^3)
# Computes a max weight perfect matching in a bipartite graph
# for min weight matching, simply negate the weights.
# https://github.com/xtof-durr/makeSimple/blob/master/Munkres/kuhnMunkres.py

""" Global variables:
       n = number of vertices on each side
       U,V vertex sets
       lu,lv are the labels of U and V resp.
       the matching is encoded as 
       - a mapping Mu from U to V, 
       - and Mv from V to U.
    
    The algorithm repeatedly builds an alternating tree, rooted in a
    free vertex u0. S is the set of vertices in U covered by the tree.
    For every vertex v, T[v] is the parent in the tree and Mv[v] the
    child.

    The algorithm maintains minSlack, s.t. for every vertex v not in
    T, minSlack[v]=(val,u1), where val is the minimum slack
    lu[u]+lv[v]-w[u][v] over u in S, and u1 is the vertex that
    realizes this minimum.

    Complexity is O(n^3), because there are n iterations in
    maxWeightMatching, and each call to augment costs O(n^2). This is
    because augment() makes at most n iterations itself, and each
    updating of minSlack costs O(n).
    """

def improveLabels(val):
    """ change the labels, and maintain minSlack. 
    """
    for u in S:
        lu[u] -= val
    for v in V:
        if v in T:
            lv[v] += val
        else:
            minSlack[v][0] -= val

def improveMatching(v):
    """ apply the alternating path from v to the root in the tree. 
    """
    u = T[v]
    if u in Mu:
        improveMatching(Mu[u])
    Mu[u] = v
    Mv[v] = u

def slack(u,v): return lu[u]+lv[v]-w[u][v]

def augment():
    """ augment the matching, possibly improving the lablels on the way.
    """
    while True:
        # select edge (u,v) with u in S, v not in T and min slack
        ((val, u), v) = min([(minSlack[v], v) for v in V if v not in T])
        assert u in S
        assert val>=0
        if val>0:        
            improveLabels(val)
        # now we are sure that (u,v) is saturated
        assert slack(u,v)==0
        T[v] = u                            # add (u,v) to the tree
        if v in Mv:
            u1 = Mv[v]                      # matched edge, 
            assert not u1 in S
            S[u1] = True                    # ... add endpoint to tree 
            for v in V:                     # maintain minSlack
                if not v in T and minSlack[v][0] > slack(u1,v):
                    minSlack[v] = [slack(u1,v), u1]
        else:
            improveMatching(v)              # v is a free vertex
            return

def maxWeightMatching(weights):
    """ given w, the weight matrix of a complete bipartite graph,
        returns the mappings Mu : U->V ,Mv : V->U encoding the matching
        as well as the value of it.
    """
    global U,V,S,T,Mu,Mv,lu,lv, minSlack, w
    w  = weights
    n  = len(w)
    U  = V = range(n)
    lu = [ max([w[u][v] for v in V]) for u in U]  # start with trivial labels
    lv = [ 0                         for v in V]
    Mu = {}                                       # start with empty matching
    Mv = {}
    while len(Mu)<n:
        free = [u for u in V if u not in Mu]      # choose free vertex u0
        u0 = free[0]
        S = {u0: True}                            # grow tree from u0 on
        T = {}
        minSlack = [[slack(u0,v), u0] for v in V]
        augment()
    #                                    val. of matching is total edge weight
    val = sum(lu)+sum(lv)
    return (Mu, Mv, val)


import math
def get_cost_matrix(vid, turk, truth, turk_sorted_lidlist, truth_sorted_lidlist, window_size):
    matrix = []

    if len(turk_sorted_lidlist) > len(truth_sorted_lidlist):
        for turk_id in turk_sorted_lidlist:
            row = []
            turk_label = turk[vid][turk_id]
            for index in range(0, len(turk_sorted_lidlist)):
                if index < len(truth_sorted_lidlist):
                    true_id = truth_sorted_lidlist[index]
                    true_label = truth[vid][true_id]
                    distance = math.fabs(float(turk_label["time"]) - float(true_label["time"])) * 100
                    if distance > window_size * 100:
                        distance = 1000000
                else:  # fill with max distance
                    true_id = "null"
                    distance = 1000000
                # print turk_id, true_id, (-1 * int(distance))
                # invert the value because we're optimizing for maximum weight, not minimum
                row.append(-1 * int(distance))
            matrix.append(row)
    else:
        # for turk_id in turk_sorted_lidlist:
        for i in range(0, len(truth_sorted_lidlist)):
            row = []
            if i < len(turk_sorted_lidlist):
                turk_id = turk_sorted_lidlist[i]
                turk_label = turk[vid][turk_id]
                for j in range(0, len(truth_sorted_lidlist)):
                    true_id = truth_sorted_lidlist[j]
                    true_label = truth[vid][true_id]
                    distance = math.fabs(float(turk_label["time"]) - float(true_label["time"])) * 100
                    if distance > window_size * 100:
                        distance = 1000000
                    # print turk_id, true_id, (-1 * distance)
                    # invert the value because we're optimizing for maximum weight, not minimum
                    row.append(-1 * int(distance))
            else:
                for j in range(0, len(truth_sorted_lidlist)):
                    turk_id = "null"
                    true_id = truth_sorted_lidlist[j]
                    distance = 1000000
                    # print turk_id, true_id, (-1 * int(distance))
                    row.append(-1 * int(distance))
            matrix.append(row)        
    # print matrix
    return matrix


# a small example 
#print maxWeightMatching([[1,2,3,4],[2,4,6,8],[3,6,9,12],[4,8,12,16]])

# read from standard input a line with n
# then n*n lines with u,v,w[u][v]

if __name__=='__main__':
    # n = int(raw_input())
    # w = [[0 for v in range(n)] for u in range(n)]
    # for _ in range(n*n):
    #     u,v,w[u][v] = map(int, raw_input().split())
    # print maxWeightMatching(w)
    w = [[-10, -20, -10000, -10000], [-9, -21, -10000, -10000], [-10000, -10000, -5, -10000], [-8, -22, -10000, -10000]]
    print w
    print maxWeightMatching(w)
    # print maxWeightMatching([[-31, -3, -2, -31, -31], [-31, -31, -2, -4, -31], [-31, -31, -31, -1, -5], [-31, -31, -31, -31, -6], [-31, -31, -31, -31, -31]])
